<?
class FormProcessor_SendSocialDBEmail extends FormProcessor
{
    protected $db = null;
    protected $config=null;
    private $_photoId=null;
    private $_emailAddress=null;

    protected $_eventImage=null;
    protected $_eventName='';
    protected $_dataArr=array();
    protected $_brandName='';
    protected $_brand=null;
    protected $_event=null;
    protected $_emailBody=null;

    public function __construct($db,$config)
    {
        parent::__construct();
        $this->db = $db;
        $this->config=$config;
    }

    /*
    * USED WITH AJAX CALL
    */
    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }

    /*
    * ESTABLISHED MOBILE AWARENESS
    * BECAUSE SESSIONS ARE HANDLED DIFFRENTLY (STATELESS)
    */
    public function apiLogin($flag){
        $this->_apiLogin=(bool)$flag;
        $this->_clearIdentity=(bool)!$flag;
    }


    public function process(Zend_Controller_Request_Abstract $request)
    {
        //Supports JSON and Simple REST
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_photoId=$phpNative->photoID;
            $this->_emailAddress=$phpNative->emailAddress;
        }else{
            $this->_photoId=strlen($request->getParam("photoID"))>0?$request->getParam("photoID"):null;  //Optional
            $this->_emailAddress=strlen($request->getParam("emailAddress"))>0?$request->getParam("emailAddress"):null;  //Optional
        }

        //Check
        if  (strlen($this->_photoId) == 0 )$this->addError('photoId','A photo Id is required.');
        if  (strlen($this->_emailAddress) == 0 )$this->addError('emailAddress','An email address is required.');

        if (!$this->hasError()){
            $this->sendDbEmail($this->_photoId,$this->_emailAddress);
        }
        return !$this->hasError();
    }

    public function sendDbEmail($photoId,$emailAddress){
        $config=Zend_Registry::get('config');
        $event= new DatabaseObject_Event($this->db);
        $profileEnum=$event->profileEnum;
        $eventImage= new DatabaseObject_File_Image_Event($event);

        if($eventImage->load((int)$photoId)){
            $event->load($eventImage->owner_id);
            $this->_event=$event;

            $brand= new DatabaseObject_Brand($this->db);
            $brand->load($event->brand_id);
            $this->_brand=$brand;
            $this->_eventImage=$eventImage;
            //Standard
            $html=true;
            $dataArr=array();

            //**Constant Block**********EVENT IMAGE   ******************-TODO:Get Full Link of Image
            $dataArr['eventImage']=$this->_eventImage;
            $dataArr['imgURL']=$this->_eventImage->fullpath_createThumbnail("300","300",$config);
            //**Constant Block******** EVENT NAME **************************
            $dataArr['eventName']=$this->_event->event_name;

            //**Constant Block******* EVENT BRANDNAME *********************
            $dataArr['brandName']=$this->_brand->brand_name;

            //***Link Block ****** FULL URL **************************
            $dataArr['fullURL']=sprintf("http://%s/@%s/%s/%s",
                $config->webhost,
                $this->_brand->brand_name,
                preg_replace( '/\s+/', '',$this->_event->event_name),
                $this->_eventImage->filename
            );

            //**Constant Block ******Facebook Share
            $dataArr['facebookLink']=sprintf("http://www.facebook.com/sharer.php?u=%s&t=My Event Pix from %s",
                $dataArr['fullURL'],
                $dataArr['eventName']);


            //**Constant Block ******Twitter Share
            $dataArr['twitterLink']=sprintf("https://twitter.com/intent/tweet?source=webclient&text=Check+out+my+pic+from+%s+%s",
                $dataArr['eventName'],
                $dataArr['fullURL']);

            //**Constant Block ******GooglePlus Share
            $dataArr['googleplusLink']=sprintf("https://plus.google.com/share?url=%s",
                $dataArr['fullURL']);
            //******************************************************************
            //Background Upload
            if(strlen($this->_event->profile->$profileEnum['brandimgmemailbgId'])){
                $theImg=$this->_event->profile->assetImgObjectForName('emailbg');
                $thumbPath=$theImg->fullpath_createThumbnail(2000,2000,$this->config);
                $dataArr['backgroundImage']=$thumbPath;
            }else{
                $dataArr['backgroundImage']='http://www.applaud-designs.com/Email/Images/hkn_background.png'; //sprintf("%s/tmp/images/background7.png",$config->webhost);
            }

            //********************************USING CUSTOMIZED DATA- Mostly in the Script/HTML
            $dataArr['backgroundColor']="#228b22";
            $dataArr['eventFontColor']="#ffffff";
            //Facebook Connect
            $dataArr['facebookConnect']=sprintf("https://www.facebook.com/heineken");
            //Use custom sheet plus the static data

            //Logos
            $dataArr['facebookLogo']=sprintf("%s/skin/images/facebook_100x100.png",$config->webhost);
            $dataArr['twitterLogo']=sprintf("%s/skin/images/twitter_100x100.png",$config->webhost);
            $dataArr['googleplusLogo']=sprintf("%s/skin/images/googleplus_100x100.png",$config->webhost);

            $this->_dataArr=$dataArr;

            //Populate Data
            $eventTemplateEmail= new DatabaseObject_EventTemplateEmail($this->db);
            $eventTemplateEmail->loadWithEventId($event->getId());
            $emailBody=sprintf('Trouble viewing this email?<a href="%s"> Click here</a> to see your picture.<br /><br />%s',$dataArr['fullURL'],$eventTemplateEmail->tpl);
            $emailSubject=$eventTemplateEmail->subject;

            //Facebook
            $emailBody=str_replace("(*fullURL*)",$dataArr['fullURL'],$emailBody);
            $emailBody=str_replace("(*imgURL*)",$dataArr['imgURL'],$emailBody);
            $emailBody=str_replace("(*imgLURL*)",$dataArr['imgURL'],$emailBody); //For Landscape
            $emailBody=str_replace("(*eventName*)",$dataArr['eventName'],$emailBody);
            $emailBody=str_replace("(*brandName*)",$dataArr['brandName'],$emailBody);
            $emailBody=str_replace("(*backgroundImage*)",$dataArr['backgroundImage'],$emailBody);
            $emailBody=str_replace("(*twitterLink*)",$dataArr['twitterLink'],$emailBody);
            $emailBody=str_replace("(*facebookLink*)",$dataArr['facebookLink'],$emailBody);
            $emailBody=str_replace("(*googleplusLink*)",$dataArr['googleplusLink'],$emailBody);

            $emailBody=str_replace("(*facebookLogo*)",$dataArr['facebookLogo'],$emailBody); //Size set by template
            $emailBody=str_replace("(*twitterLogo*)",$dataArr['twitterLogo'],$emailBody);
            $emailBody=str_replace("(*googleplusLogo*)",$dataArr['googleplusLogo'],$emailBody);

            $this->_emailBody=$emailBody;

            if(SavCo_ZendExtend::Email_FromName_From_To_EmailSubject_EmailBody_isHTML($this->_brand->brand_name,'noreply@tracks.ponyengine.com',$emailAddress,$emailSubject,$emailBody,$html)){
                //echo "Email has been sent";
            }

        }else{
            $this->addError('eventImage', 'Could not load the image for this event');
        }

        return !$this->hasError();
    }

     public function sendEmail($photoId,$emailAddress){
        $config=Zend_Registry::get('config');
        $event= new DatabaseObject_Event($this->db);
        $profileEnum=$event->profileEnum;
        $eventImage= new DatabaseObject_File_Image_Event($event);

        if($eventImage->load((int)$photoId)){
            $event->load($eventImage->owner_id);
            $this->_event=$event;

            $brand= new DatabaseObject_Brand($this->db);
            $brand->load($event->brand_id);
            $this->_brand=$brand;

            $this->_eventImage=$eventImage;

            //Standard
            $dataArr=array();
            $tpl="socialsharemail.tpl";
            $html=true;
            $fromName="Event2Pix";


            $dataArr['eventImage']=$this->_eventImage;
            $dataArr['backgroundColor']="#228b22";

           //USING CUSTOMIZED DATA
            if(strlen($this->_event->profile->$profileEnum['brandimgmemailbgId'])){
                $theImg=$this->_event->profile->assetImgObjectForName('emailbg');
                $thumbPath=$theImg->fullpath_createThumbnail(2000,2000,$this->config);
                $dataArr['backgroundImage']=$thumbPath;
            }else{
                $dataArr['backgroundImage']=sprintf("%s/tmp/images/effectsBottleOverlay.jpg",$config->webhost);
            }

            if(strlen($this->_event->profile->$profileEnum['emailMsg'])){
                $dataArr['emailMsg']= $this->_event->profile->$profileEnum['emailMsg'];
            }else{
                $dataArr['emailMsg']= sprintf("Hello!<br /><br /> Thanks for hanging out with us at %s .<br/><br/>
                                    Your picture is ready to share!",$this->_event->event_name);
            }

            $dataArr['eventFontColor']="#ffffff";
            $dataArr['eventName']=$this->_event->event_name;
            $dataArr['brandName']=$this->_brand->brand_name;
            $dataArr['fullURL']=sprintf("http://%s/@%s/%s/%s",
                $config->webhost,
                $this->_brand->brand_name,
                preg_replace( '/\s+/', '',$this->_event->event_name),
                $this->_eventImage->filename
            );

            //FullURL
            $dataArr['facebookLink']=sprintf("http://www.facebook.com/sharer.php?u=%s&t=My Event Pix from %s",
                $dataArr['fullURL'],
                $dataArr['eventName']);

            $dataArr['facebookConnect']=sprintf("https://www.facebook.com/heineken");

            //Twitter Link
            $dataArr['twitterLink']=sprintf("https://twitter.com/intent/tweet?source=webclient&text=Check+out+my+pic+from+%s+%s",
                $dataArr['eventName'],
                $dataArr['fullURL']);

            $emailFrom=Zend_Registry::get('config')->email->from->email;
            if(SavCo_ZendExtend::Email_FromName_From_To_Data_Template_isHTML($this->_brand->brand_name,'noreply@tracks.ponyengine.com',$emailAddress,$dataArr,$tpl,$html)){
                //echo "Email has been sent";
            }

        }else{
            $this->addError('eventImage', 'Could not load the image for this event');
        }

        return !$this->hasError();
    }



    public function eventPhotoId(){
        return $this->_eventPhotoId;
    }

    public function emailBody(){
        return $this->_emailBody;
    }
    public function dataArr(){
        return $this->_dataArr;
    }
}

?> 