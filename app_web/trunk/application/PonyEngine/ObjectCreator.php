<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 5/19/13
 * Time: 3:52 AM
 * To change this template use File | Settings | File Templates.
 * The purpose of this class is to generate the php and mysql of an object based on DatabaseObject
 *
 *
 *
 */
class PonyEngine_ObjectCreator{
    protected static $constTable='bets';
    protected static $constId='bet_id';
    public $profile= null;
    protected $_imageIds = array();
    public $profileEnum=array();

    public function __construct($db)
    {
        $this->add('bettype_id');
        $this->add('user_id');
        $this->add('bet_description');
        $this->add('bet_status',null);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
        $this->add('tsEnded',null);
        $this->profile= new Profile_Event($db,$this);
        $this->profileEnum=DatabaseObject_Enum_EventProfile::GetEnums($db);
    }

    public function attachUsers($bool){
    }

    protected function postLoad()
    {
        $this->_imageIds=DatabaseObject_File_Image_Event::GetIdsForEventId($this->getDb(),$this->getId());
        $this->_templateEmail= new DatabaseObject_EventTemplateEmail($this->getDb());
        $this->_templateEmail->loadWithEventId((int)$this->getID());

        //Profile
        $this->profile->setEvent($this);
        $this->profile->load();
        $this->event_length=0;

        return true;
    }

    protected function postInsert(){
        $this->profile->setEvent($this);
        $this->profile->save(false);

        return true;
    }

    protected function postUpdate()
    {
        $this->profile->save(false);
        return true;
    }

    public function getImageWithFilename($filename){
        $newImage= new DatabaseObject_File_Image_Event($this);
        if($newImage->loadByEventIdFilename($this->getId(),$filename)){
            return $newImage;
        }
        return null;
    }

    public static function GetEventsWithOptions($db, $options = array())
    {
        $brands=array();
        // initialize the options
        $defaults = array('user_id' => array());

        foreach ($defaults as $k => $v)
            $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;

        $select = $db->select();
        $select->from(array('l' => 'events'), 'l.*');

        // filter results on specified post ids (if any)
        if (count($options['user_id']) > 0)
            $select->where('l.user_id in (?)', $options['user_id']);

        if (count($options['brand_id']) > 0)
            $select->where('l.brand_id in (?)', $options['brand_id']);

        //$select->where('tsEnd is null');
        $select->order(array('event_name ASC'));
        // fetch post data from database
        $data = $db->fetchAll($select);

        $events=array();
        //$events = parent::BuildMultiple($db, __CLASS__, $data);
        foreach($data as $anEvent){
            $anEventObj= new DatabaseObject_Event($db);
            $anEventObj->load($anEvent['event_id']);
            $events[]=$anEventObj;
        }

        return $events;
    }

    public static function GetEventsForUserId($db,$theId){
        $brands=array();

        $select ="Select event_id FROM events
            inner join brands_users on events.brand_id=brands_users.brand_id
             where brands_users.user_id=$theId";

        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();
        foreach($rowset as $row){
            $brands[]=$row['event_id'];
        }
        return $brands;
    }

    /* public static function GetLiveEventsForUserId($db,$theId){
                $brands=array();

                $select ="Select event_id FROM events
                inner join brands_users on events.brand_id=brands_users.brand_id
                 where brands_users.user_id=$theId AND events.tsEnd is NULL";

                $stmt=$db->query($select);
                $rowset=$stmt->fetchAll();
                foreach($rowset as $row){
                    $brands[]=$row['event_id'];
                }
                return $brands;
            }
    */

    public function endEvent(){
        if(!$this->tsEnd){
            $this->tsEnd=time();
            $this->save();
            return true;
        }
        return false;
    }

    protected function preDelete()
    {
        $eventId=$this->getId();
        $eventName=$this->event_name;
        $message="Deleting Events $eventName($eventId)";
        SavCo_ConstantArr::getLogEvent()>notice($message,1);
        $this->profile->delete();
        return true;
    }

    public function imageIds(){

        return $this->_imageIds;
    }

    public function statsPerDay(){
        $daysOfEvent=$this->daysOfEvent();
        $realDays=$this->realDaysOfUsage();
        $daysStats=array();
        $daysMerge=array_merge($daysOfEvent,$realDays);
        sort($daysMerge,SORT_ASC);
        $daysUniqueMerge=array_unique($daysMerge,SORT_NUMERIC);

        foreach($daysUniqueMerge as $aDay){
            $aDayStats=$this->imagesSocialSharedStatsCountForDay($aDay);
            $aDayStats['dayOfEvent']=in_array($aDay,$daysOfEvent);
            $aDayStats['date']=date('m/d/Y',$aDay);
            $aDayStats['taken']=$this->imageTakenCountForDay($aDay);
            $aDayStats['emailed']=$this->imagesWithEmailsCountForDay($aDay);
            $aDayStats['mmsed']=$this->imagesWithMMSCountForDay($aDay);
            $daysStats[]=$aDayStats;
        }
        return $daysStats;
    }

    public function daysOfEvent(){
        $days=array();
        $profileEnum=$this->profileEnum;
        $numDays = abs($this->profile->$profileEnum['startdate'] - $this->profile->$profileEnum['enddate'])/60/60/24;
        $numDays=$numDays+1;
        for ($i = 0; $i < $numDays; $i++) {
            // echo date('Y-m-d', strtotime("+{$i} day", $this->profile->$profileEnum['startdate'])) . '<br />';
            $days[]=strtotime("+{$i} day", $this->profile->$profileEnum['startdate']);
        }
        return $days;
    }

    public function realDaysOfUsage(){
        //find the min and max timestamps
        $select =sprintf("SELECT tsCreated FROM events_images WHERE owner_id=%d",
            $this->getId());

        $stmt=$this->getDb()->query($select);
        $rowset=$stmt->fetchAll();

        $realStartDate=min($rowset);
        $realEndDate=max($rowset);

        $realStartDateMid=strtotime("midnight",$realStartDate["tsCreated"]);
        $realEndDateMid=strtotime("midnight",$realEndDate["tsCreated"]);

        $days=array();
        $profileEnum=$this->profileEnum;
        $numDays =$realStartDateMid!=$realEndDateMid?abs($realStartDateMid - $realEndDateMid)/60/60/24:0;
        $numDays=$numDays+1;
        for ($i = 0; $i < $numDays; $i++) {
            // echo date('Y-m-d', strtotime("+{$i} day", $this->profile->$profileEnum['startdate'])) . '<br />';
            $days[]=strtotime("+{$i} day", $realStartDateMid);
        }
        return $days;
    }

    public function imagesWithEmailsCountForDay($timeStamp){
        if(count($this->_imageIds)>0){
            $dayStartTS=strtotime("midnight", $timeStamp);
            $dayEndTS=strtotime("tomorrow", $timeStamp) - 1;

            $select =sprintf("SELECT image_id FROM events_images INNER JOIN events_images_profile ON event_image_id=image_id WHERE image_id IN (%s) AND tsCreated>=%d AND tsCreated<=%d AND profile_key='eip1'",
                implode(',',$this->_imageIds),
                $dayStartTS,
                $dayEndTS);


            $stmt=$this->getDb()->query($select);
            $rowset=$stmt->fetchAll();

            return count($rowset);
        }
        return 0;
    }

    public function imagesWithMMSCountForDay($timeStamp){
        if(count($this->_imageIds)>0){
            $dayStartTS=strtotime("midnight", $timeStamp);
            $dayEndTS=strtotime("tomorrow", $timeStamp) - 1;

            $select =sprintf("SELECT image_id FROM events_images INNER JOIN events_images_profile ON event_image_id=image_id WHERE image_id IN (%s) AND tsCreated>=%d AND tsCreated<=%d AND profile_key='eip2'",
                implode(',',$this->_imageIds),
                $dayStartTS,
                $dayEndTS);


            $stmt=$this->getDb()->query($select);
            $rowset=$stmt->fetchAll();

            return count($rowset);
        }
        return 0;
    }

    //Social Sharing
    private function imagesSocialSharedStatsCountForDay($timeStamp){
        $stats['emailDevice']=0;
        $stats['printDevice']=0;
        $stats['facebookDevice']=0;
        $stats['twitterDevice']=0;
        $stats['googleplusDevice']=0;
        $stats['mmsDevice']=0;

        if(count($this->_imageIds)>0){
            $dayStartTS=strtotime("midnight", $timeStamp);
            $dayEndTS=strtotime("tomorrow", $timeStamp) - 1;

            $select =sprintf("SELECT statsocial_id,device_id FROM events_images_statsocials WHERE image_id IN (%s) AND tsCreated>=%d AND tsCreated<=%d",
                implode(',',$this->_imageIds),
                $dayStartTS,
                $dayEndTS);

            $stmt=$this->getDb()->query($select);
            $rows=$stmt->fetchAll();

            foreach($rows as $socialStat){
                if($socialStat["statsocial_id"]==1 && $socialStat["device_id"]==2)$stats['emailDevice']++;
                if($socialStat["statsocial_id"]==2 && $socialStat["device_id"]==2)$stats['printDevice']++;
                if($socialStat["statsocial_id"]==3 && $socialStat["device_id"]==2)$stats['facebookDevice']++;
                if($socialStat["statsocial_id"]==4 && $socialStat["device_id"]==2)$stats['twitterDevice']++;
                if($socialStat["statsocial_id"]==5 && $socialStat["device_id"]==2)$stats['googleplusDevice']++;
                if($socialStat["statsocial_id"]==6 && $socialStat["device_id"]==2)$stats['mmsDevice']++;
            }
        }
        return $stats;
    }


    public function imageTakenCountForDay($timeStamp){
        if(count($this->_imageIds)>0){
            $dayStartTS=strtotime("midnight", $timeStamp);
            $dayEndTS=strtotime("tomorrow", $timeStamp) - 1;

            $select =sprintf("SELECT %s FROM %s WHERE %s IN (%s) AND tsCreated>=%d AND tsCreated<=%d",
                'image_id',
                'events_images',
                'image_id',
                implode(',',$this->_imageIds),
                $dayStartTS,
                $dayEndTS);

            $stmt=$this->getDb()->query($select);
            $rowset=$stmt->fetchAll();

            return count($rowset);
        }
        return 0;
    }

    public static function  GetTable(){
        return DatabaseObject_Event::$constTable;
    }

    public function brandAssetByName($assetName){
        //Can start indexing by overlay, bg, etc
        $imageAsset= new DatabaseObject_File_Image_Brandimgasset($this->getDb());
        $profileEnum=$this->profileEnum;
        switch($assetName){
            case 'overlay':
                $imageId=$this->$profileEnum['brandimgoverlayId'];
                if(!$imageAsset->load($imageId))return null;
                break;
            default:
                $imageId=$this->$profileEnum['brandimgoverlayId'];
                if(!$imageAsset->load($imageId))return null;
                break;
                return $imageAsset;
        }



        return $imageAsset;
    }

    public function templateEmail(){
        return $this->_templateEmail;
    }

    public function updateTemplateEmail($subject,$htmlContent){
        //Check here for html errors- etc- cross scripting
        $this->_templateEmail->subject= $subject;
        $this->_templateEmail->tpl= $htmlContent;
        $this->_templateEmail->save();
    }

    public function brandDefaultUI($defaultEvent=null){
        if($defaultEvent==NULL) {
            $aBrand= new DatabaseObject_Brand($this->getDb());
            $aBrand->load($this->brand_id);
            $defaultEvent= new DatabaseObject_Event($this->getDb());
            $defaultEvent->load((int)$aBrand->default_event_id);
        }
        //USER INTERFACE
        //Background Screen
        if($defaultEvent->profile->e11){
            $this->profile->e11=$defaultEvent->profile->e11;
        }

        //Save/Upload Button
        if($defaultEvent->profile->e12){
            $this->profile->e12=$defaultEvent->profile->e12;
        }

        //E-mail Button
        if($defaultEvent->profile->e13){
            $this->profile->e13=$defaultEvent->profile->e13;
        }

        //Print Button
        if($defaultEvent->profile->e14){
            $this->profile->e14=$defaultEvent->profile->e14;
        }

        //MMS Button
        if($defaultEvent->profile->e15){
            $this->profile->e15=$defaultEvent->profile->e15;
        }

        //Facebook Button
        if($defaultEvent->profile->e16){
            $this->profile->e16=$defaultEvent->profile->e16;
        }

        //Twitter Button
        if($defaultEvent->profile->e17){
            $this->profile->e17=$defaultEvent->profile->e17;
        }

        //GooglePlus Button
        if($defaultEvent->profile->e18){
            $this->profile->e18=$defaultEvent->profile->e18;
        }
    }

    public function brandDefaultPhotoEffects($defaultEvent=NULL){
        if($defaultEvent==NULL) {
            $aBrand= new DatabaseObject_Brand($this->getDb());
            $aBrand->load($this->brand_id);
            $defaultEvent= new DatabaseObject_Event($this->getDb());
            $defaultEvent->load((int)$aBrand->default_event_id);
        }
        //Accent
        if($defaultEvent->profile->e9){
            $this->profile->e9=$defaultEvent->profile->e9;
        }

        //Overlay
        if($defaultEvent->profile->e4){
            $this->profile->e4=$defaultEvent->profile->e4;
        }

        //Handle-Y-Position
        if($defaultEvent->profile->e10){
            $this->profile->e10=$defaultEvent->profile->e10;
        }

        //Filter
        //FilterId
        if($defaultEvent->profile->e7){
            $this->profile->e7=$defaultEvent->profile->e7;
        }
        //Has Vignette
        if($defaultEvent->profile->e8){
            $this->profile->e8=$defaultEvent->profile->e8;
        }
        //Color Tint
        if($defaultEvent->profile->e19){
            $this->profile->e19=$defaultEvent->profile->e19;
        }

        //Accent On Left
        if($defaultEvent->profile->e20){
            $this->profile->e20=$defaultEvent->profile->e20;
        }

    }

    public function brandDefaultSocial($defaultEvent=NULL){
        if($defaultEvent==NULL) {
            $aBrand= new DatabaseObject_Brand($this->getDb());
            $aBrand->load($this->brand_id);
            $defaultEvent= new DatabaseObject_Event($this->getDb());
            $defaultEvent->load((int)$aBrand->default_event_id);
        }
        if($defaultEvent->templateEmail()){
            $subject=$defaultEvent->templateEmail()->subject;
            $html= $defaultEvent->templateEmail()->tpl;
            $this->updateTemplateEmail($subject,$html);
        }
        //Email BG Image
        if($defaultEvent->profile->e5){
            $this->profile->e5=$defaultEvent->profile->e5;
        }

    }



    public function setWithBrandDefault(){
        //Get the Brand
        $aBrand= new DatabaseObject_Brand($this->getDb());
        if($aBrand->load($this->brand_id)){
            //Get the Event
            if($aBrand->default_event_id){
                $defaultEvent= new DatabaseObject_Event($this->getDb());
                if($defaultEvent->load((int)$aBrand->default_event_id)){
                    //UI
                    $this->brandDefaultUI($defaultEvent);

                    //PHOTO EFFECTS
                    $this->brandDefaultPhotoEffects($defaultEvent);

                    //SOCIAL SETTINGS
                    $this->brandDefaultSocial($defaultEvent);

                    return $this->save();
                }//Check that event does exist
            }//Check that there is an EventId set as default.jpg
        }//Check that brand exists
        return false; // No Brand default.jpg
    }



    public static function BetsArrayRepresentationForFilterType(DatabaseObject $user,$filterType,$config,$user,$limit,$offset,$lastId,$lat,$lon){
        $bets=array();
        $betsTable=DatabaseObject_Bet::$constTable;

        if(strcmp($filterType,'nearby')!=0){
            //  $select = $user->getDb()->select();
            // $select->from($betsTable,"$betsTable.bet_id");
            // $select->joinLeft("bets_flags","$videosTable.bet_id=betss_flags.vokel_id");

            switch($filterType){
                // case 'current':
                // break;
                // case 'top5':
                //break;
                case 'tmp':
                    //$select->where("$videosTable.user_id =".$ownerObj->getId());
                    break;

                default:
                    $teams=array();
                    $aBet['imgURL']='http://www.gambling911.com/files/imagecache/slide_image/publisher/Cowboys-vs-Redskins-Line-122312L.jpg';
                    $aBet['id']='Cowboys Vs Redskins';
                    $aBet['name']='Cowboys';
                    //Team1
                    $team['id']=10;
                    $team['name']='Cowboys';
                    $team['score']=23;
                    $teams[]=$team;
                    //Team2
                    $team['id']=25;
                    $team['name']='Redskins';
                    $team['score']=46;
                    $teams[]=$team;
                    $aBet['teams']=$teams;
                    $aBet['count']=4;
                    $aBet['tsEnded']=time();
                    $aBet['tsCreated']=time();
                    $bets[]=$aBet;
                    break;
            }
            /* //Conditional
           if($lastId)$select->where("$videosTable.file_id >".$lastId);
           $select->limit((int)$limit,(int)$offset);
           //Standard Filter
           $select->where('tsDeleted is NULL');
           $select->where('vf_id is NULL');
           $select->where("$videosTable.file_id is not NULL");

           //Standard Order
           $select->order("$videosTable.tsCreated DESC");*/


        }else{
            // $select=" SELECT *, ((ACOS((SIN(users_videos.lat /57.2958) * SIN( $lat /57.2958)) +(COS(users_videos.lat /57.2958) * COS( $lat /57.2958) * COS( $lon /57.2958 - users_videos.lon /57.2958)))) * 6378.7) AS Distance";
            // $select.=" FROM `users_videos` LEFT JOIN `vokels_flags` ON `users_videos`.file_id=`vokels_flags`.vokel_id";
            // $select.=" WHERE `users_videos`.`lat` IS NOT NULL  AND file_id > $lastId AND vf_id is NULL";
            // $select.=" ORDER BY Distance ASC, `users_videos`.tsCreated DESC LIMIT $offset, $limit";  //,$offset

            $select=" SELECT *, ((ACOS((SIN(users_videos.lat /57.2958) * SIN( $lat /57.2958)) +(COS(users_videos.lat /57.2958) * COS( $lat /57.2958) * COS( $lon /57.2958 - users_videos.lon /57.2958)))) * 6378.7) AS Distance";
            $select.=" FROM `users_videos` LEFT JOIN `vokels_flags` ON `users_videos`.file_id=`vokels_flags`.vokel_id";
            $select.=" WHERE `users_videos`.`lat` IS NOT NULL  AND file_id > $lastId AND vf_id is NULL AND tsDeleted is NULL";
            $select.=" ORDER BY Distance ASC LIMIT $offset, $limit";  //,$offset
        }

        /*
                $stmt=$ownerObj->getDb()->query($select);
                $data=$stmt->fetchAll();
                $vokels=parent::BuildMultiple($ownerObj,$class, $data);
        */

        return $bets;
    }

}