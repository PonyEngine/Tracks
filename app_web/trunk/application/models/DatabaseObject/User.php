<?
/*
 * Point System
 * =============
 * Pts         Level     WIN    LOSS
 *    0 - 100    1       +25     +10
 *  101 - 200    2
 *  201 - 300    3
 *  301 -  400   4
 *  401 - 900    5
 *  901 - 1000   6
 * 1000  +       7
 *
 *
 * ACTIONS       Pts
 * =======       ===
 * Create Bet    +20
 * Accept Bet    +10
 * Reject Bet    +0
 *
 *
 */

class DatabaseObject_User extends DatabaseObject{
    protected static $constTable='users';
    protected static $constId='user_id';
    public $profile= null;
    public $profileEnum=null;
    protected $_basicInfo=false;
    public $images = array();
    public $session=null;
    public $profileImageURL=null;
    private $tempArr;

    //Custom
    const MAX_PRESENCE_HISTORY=10;

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_User::$constTable,DatabaseObject_User::$constId);
        $this->add('fbId',null);
        $this->add('usernameEmail',null);
        $this->add('password',null);
        $this->add('profileName',null);
        $this->add('user_type','member');
        $this->add('bucks',250);
        $this->add('points',0);
        $this->add('level',1);
        $this->add('tsCreated',time());
        $this->add('tsLastLogin',null);
        $this->add('expiry',null);
        $this->profile= new Profile_User($db);
        $this->session= new DatabaseObject_UserSession($db);
        $this->profileEnum=DatabaseObject_Enum_UserProfile::GetEnums($db);
    }

    public function setBasicInfoOnly($basicInfo){
        $this->_basicInfo=$basicInfo;
    }

    public function deleteSession(){
        $this->session->delete();
        $this->session=NULL;
        return true;
    }

    public function arrayRepresentationSimple($config){
        $profileEnum=$this->profileEnum;
        $simple['userId']=$this->getId();
        $simple['usernameEmail']=sprintf("*%s",$this->profileName); //Temporary- SavCo Fix- Will be changed in Next Build
        $simple['profileName']=sprintf("*%s",$this->profileName);
        $simple['firstName']=$this->profile->$profileEnum['firstName'];
        $simple['fullName']=sprintf("%s %s",$this->profile->$profileEnum['firstName'],$this->profile->$profileEnum['lastName']);
        $simple['picURL']=$this->picURL($config);
        return $simple;
    }

    public function arrayRepresentationSimpleProfile($config){
        $profileEnum=$this->profileEnum;
        $simpleProfile=$this->arrayRepresentationSimple($config);
        $simple['location']=$this->profile->$profileEnum['location_home'];
        return $simpleProfile;
    }

    public function arrayRepresentationProfileBase($config){
        $profileEnum=$this->profileEnum;
        $thisProfile['userId']=$this->getId();
        $thisProfile['firstName']=$this->profile->$profileEnum['firstName'];
        $thisProfile['middleName']=$this->profile->$profileEnum['middleName'];
        $thisProfile['lastName']=$this->profile->$profileEnum['lastName'];
        $thisProfile['gender']=$this->profile->$profileEnum['gender'];
        $thisProfile['profileName']=sprintf("*%s",$this->profileName);
        $thisProfile['picURL']=$this->picURL($config);
        $thisProfile['bio']=$this->profile->$profileEnum['aboutMe'];
        $thisProfile['bucks']=$this->bucks;
        $thisProfile['points']=$this->points;
        $thisProfile['level']=$this->level;
        return $thisProfile;
    }

    public function arrayRepresentationProfile($config=null){
        $profileEnum=$this->profileEnum;
        $thisProfile=$this->arrayRepresentationProfileBase($config);
        $thisProfile['usernameEmail']=$this->usernameEmail;
        $thisProfile['role']=$this->user_type;
        $thisProfile['tsCreated']=$this->tsCreated;
        $thisProfile['birthdate']=$this->profile->$profileEnum['birthDate'];
        $thisProfile['phone10']=$this->profile->$profileEnum['phone10'];
        $thisProfile['location']=$this->profile->$profileEnum['location_home'];



        return $thisProfile;
    }

    public function arrayRepresentationDetailed($config=NULL){
        //Can be optimized with BuildMultiple
        $profileEnum=$this->profileEnum;
        $simple['userId']=$this->getId();
        $simple['picURL']=$this->picURL($config);
        $simple['usernameEmail']=sprintf("%s",$this->usernameEmail);
        $simple['profileName']=sprintf("%s",$this->profileName);
        $simple['user_type']=sprintf("%s",$this->user_type);

       /* $simple['brands']=array();
        foreach($this->userBrands() as $aBrand){
            $simple['brands'][$aBrand->getId()]=$aBrand->brand_name;
        }*/

        $simple['tsCreated']=sprintf("%s",date ("<b> n/j/Y </b> [h:i a]",$this->tsCreated));
        $simple['tsLastLogin']=$this->tsLastLogin?sprintf("%s",date ("<b> n/j/Y </b>  [h:i a]", $this->tsLastLogin)):'Never';
        //$simple['tsExpire']=$this->expiry?sprintf("%s",date ("<b> n/j/Y </b>  [h:i a]",$this->expiry)):'Never';
        $simple['tsExpire']=$this->expiry?sprintf("%s",date ("n/j/Y",$this->expiry)):'Never';
        return $simple;
    }



    public function arrayRepresentationSocial(){
        $config=Zend_Registry::get('config');
        $profileEnum=$this->profileEnum;
        //Social Settings
        $thisSocial['fb_id']=$this->fbId;
        $thisSocial['fb_accessToken']=$this->profile->$profileEnum['fb_accessToken'];
        $thisSocial['fb_email']=$this->profile->$profileEnum['fb_email'];
        $thisSocial['fb_picURL']=$this->profile->$profileEnum['fb_picURL'];
        $thisSocial['fb_profileURL']=$this->profile->$profileEnum['fb_profileURL'];
        return $thisSocial;
    }

    public function arrayRepresentationOpenInvites(){
        $config=Zend_Registry::get('config');
        $profileEnum=$this->profileEnum;

        $theOpenInvites=array();
        $openInvites=DatabaseObject_UserBetInvite::OpenInvitesForUserArrayRep($this);

        foreach($openInvites as $anInvite){
            $thisInvite['ubi_id']=(int)$anInvite['ubi_id'];
            $thisInvite['ub_id']=$anInvite['ub_id'];
            $thisInvite['fbId']=$anInvite['fbId'];
            $thisInvite['tsCreated']=$anInvite['tsCreated'];

            $userBet= new DatabaseObject_UserBet($this->getDb());
            $userBet->load((int)$thisInvite['ub_id']);

            $userBetUser= new DatabaseObject_User($this->getDb());
            $userBetUser->load((int)$userBet->user_id);
            $thisInvite['inviteeImgURL']=$anInvite['fbId'];
            $thisInvite['inviteMsg']=$userBet->inviteMessage();
            $thisInvite['inviterName']=$userBetUser->profile->up1;
            $thisInvite['inviterImgURL']=$userBetUser->picURL($config);
            $theOpenInvites[]=$thisInvite;
        }

        return $theOpenInvites;
    }

    public function arrayRepresentation($config){
        // $config=Zend_Registry::get('config');
        $profileEnum=$this->profileEnum;
        //Profile Info
        $thisProfile=$this->arrayRepresentationProfile($config);

      //  $thisProfile['social']=$this->arrayRepresentationSocial();

        return $thisProfile;
    }

    public  static function UserIdForFbId($fbId){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        $db =SavCo_ConstantArr::getDbase(); //why is this not pulling from the instance variable
        $select = 'SELECT user_id FROM users WHERE ';
        $select .=" fbId=$fbId ";
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)>0){
            foreach ($rowset as $row){
               return $row['user_id'];
            }
        }else{
            //error and find out why
            return null;
        }
    }

    public function picURL($config){
        $useFacebook=true;
        //Check if user wants to use facebook
        if($useFacebook){
            if($this->profile->up20){
                return sprintf("http://graph.facebook.com/%s/picture?type=large",$this->profile->up20);
            }
        }
        //Check if they have facebo
        return  $this->profileImage()->fullpath_createThumbnail(100,100,$config);
    }

    public static function GetUserWithFbId($fbId,$facebook){
        if(strlen($fbId)<1 || !$facebook) return false;
        $db =SavCo_ConstantArr::getDbase();

        try{
            $param  =   array(
                'method'  => 'users.getinfo',
                'uids'    => $fbId,
                'fields'  => 'username,first_name,middle_name,last_name,email,pic,profile_url',
                'callback'=> '');
            $userInfo   = $facebook->api($param);
        }
        catch(Exception $o){
            print_r($o); //Log this
        }
        $userObj=new DatabaseObject_User($db);
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        if(!$userObj->loadByFbId($fbId)){
            //TODO:Add code to to tell if a User already exists with the usernameEmail
            //TODO: Add code properly check for username existence and reassign a user name
            //TODO:Truly flesh out the FBlogin process- saving od data etc
            $userObj->fbId=$fbId;
            if(strlen($userInfo[0]['first_name'])>0)$userObj->profile->$userProfileFieldNameIDArr['firstName']=$userInfo[0]['first_name'];
            if(strlen($userInfo[0]['middle_name'])>0)$userObj->profile->$userProfileFieldNameIDArr['middleName']=$userInfo[0]['middle_name'];
            if(strlen($userInfo[0]['last_name'])>0)$userObj->profile->$userProfileFieldNameIDArr['lastName']=$userInfo[0]['last_name'];
            if(strlen($userInfo[0]['username'])>0)$userObj->profile->$userProfileFieldNameIDArr['fb_profilename']=$userInfo[0]['username'];
            if(strlen($userInfo[0]['email'])>0){
                $userObj->profile->$userProfileFieldNameIDArr['fb_email']=$userInfo[0]['email'];
                $userObj->usernameEmail=$userInfo[0]['email']; //required this can be changed later by user
            }else{
                $userObj->usernameEmail="";
            }
            if(strlen($userInfo[0]['pic'])>0)$userObj->profile->$userProfileFieldNameIDArr['fb_picURL']=$userInfo[0]['pic'];



            if(strlen($userInfo[0]['fb_profileURL'])>0)$userObj->profile->$userProfileFieldNameIDArr['fb_profileURL']=$userInfo[0]['profile_url'];
            //ProfileName
            if(strlen($userInfo[0]['first_name'])>0)$userObj->profileName=strtolower($userInfo[0]['first_name']).".".strtolower($userInfo[0]['last_name']);;
            $userObj->user_type="member";

            $userObj->password=SavCo_FunctionsGen::createRandomPassword(7); //No real password to use- need to make sure that not able to login this way

            $userObj->save();

            //Add Later to pull the image from facebook- also look at gravatar
            /*if($userObj->save()){
               //Processing to be done afer userId is given
                if($userObj->profile->$userProfileFieldNameIDArr['fb_picURL']){
                   $userImage= new DatabaseObject_File_Image_User($userObj);

                    if($userImage->insertFileFromURL($userObj->profile->$userProfileFieldNameIDArr['fb_picURL'])){
                        $userObj->profile->$userProfileFieldNameIDArr['profilePicImageId']=$userImage->getId();
                    }

                }
            }*/

        }else{
            //this user already exists- just login them in
            if(strlen($userInfo[0]['username'])>0)$userObj->profile->$userProfileFieldNameIDArr['fb_profilename']=$userInfo[0]['username'];
        }
        $accessToken=$facebook->getExtendedAccessToken(); //Always get accessToken- until we get the date
        if(strlen($accessToken)>0) $userObj->profile->$userProfileFieldNameIDArr['fb_accessToken']=$accessToken;


        return $userObj;
    }

    public function authenticateUser(){
        $auth = Zend_Auth::getInstance();
        $identity = $this->createAuthIdentity();
        $auth->getStorage()->write($identity);
        $this->tsLastLogin=time();
        $this->save();
    }

    public function loadByFbId($fbId){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        $db =SavCo_ConstantArr::getDbase(); //why is this not pulling from the instance variable
        $select = 'SELECT user_id FROM users WHERE ';
        $select .=" fbId=$fbId ";
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $user_id=$row['user_id'];
            }
            return $this->load($user_id);
        }else{
            //error and find out why
            return false;
        }
    }

    public function loadByProfileName($profileName){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        $db =SavCo_ConstantArr::getDbase(); //why is this not pulling from the instance variable
        $select = 'SELECT user_id FROM users WHERE ';
        $select .=' LOWER(users.profileName)="'.strtolower($profileName).'"';
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $user_id=$row['user_id'];
            }
            return $this->load($user_id);
        }else{
            //error and find out why
            return false;
        }
    }

    public function loadByValidatedSession($userid,$sess,$lat=null,$lon=null){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        $db =SavCo_ConstantArr::getDbase();
        $sessionObj= new DatabaseObject_UserSession($db);
        if($sessionObj->load($userid)){
            if($sessionObj->sessionId==$sess){
                $sessionObj->lat=$lat;
                $sessionObj->lon=$lon;
                $sessionObj->tsModified=time();
                $sessionObj->save();
                return $this->load($userid);
            }else{
                //Someone else is logged in as user
                return NULL;
            }
        }else{
            //lost session or non set
            return NULL;
        }
    }

      protected function preInsert()
    {	//Find out why the uniqueid was here - is this the salt?
        //if opened it changes password beforeused
        //$this->password=uniqid();
        return true;
    }

    protected function postLoad()
    {
        $this->profile->setUser_id($this->getId());
        $this->profile->load();
        $this->session->load($this->getId());
    }


    protected function postInsert(){
        $this->profile->setUser_id($this->getId());
        $this->profile->save(false);

        $dataArr['user']=$this;
        $dataArr['tempArr']=$this->tempArr;
        $this->sendEmail('user-register.tpl',$dataArr);

        return true;
    }


    protected function postUpdate()
    {
        $this->profile->save(false);
        return true;
    }

    protected function preDelete()
    {
        //SAVE TO LOG FILES ON DELETES- SAVE ALL VALUES
        $this->profile->delete();

        //Delete User Images
        foreach ($this->images as $anImage){
            $anImage->delete(false);
        }
        $this->logoImage=null;


        return true;
    }

    public function __set($name,$value){
        switch($name){
            case 'password':
                $this->tempArr['password']=$value;
                $value=md5($value);
                break;

            case 'user_type':
                if(!array_key_exists($value,SavCo_ConstantArr::getUserTypeNameIDArr()))
                    $value='member';
                break;
        }
        return parent::__set($name,$value);
    }

    //Validation Functions for this Object
    public static function AvailProfileNameAndValid($db,$profileName){
        //First Check if it is Valid
        if(DatabaseObject_User::IsProfileNameValid($profileName)){
            $query= sprintf('select count(*) as num from %s where profileName=?',DatabaseObject_User::$constTable);
            $result= $db->fetchOne($query,$profileName);
            return $result['num']<1;
        }
        return FALSE;
    }

    public static function IsProfileNameValid($profilename){
        if(count($profilename)==0){
            return FALSE;
        }else if(str_word_count($profilename)>1){
            return FALSE;
        }else if(!ctype_alnum($profilename)){ //Check that it only contains letters and numbers- check for localization
            return FALSE;
        }else if(strstr(strtolower($profilename),'voke')!= false){
            return FALSE;
        }

        return TRUE;
    }

    public static function UsernameEmailExists($db,$usernameEmail){
        $query= sprintf('select count(*) as num from %s where usernameEmail=?',DatabaseObject_User::$constTable);
        $result= $db->fetchOne($query,$usernameEmail);
        return $result['num']>0;
    }


    public function createAuthIdentity(){
        $identity= new stdClass;
        $identity->user_id=$this->getId();
        $identity->usernameEmail=$this->usernameEmail;
        $identity->loginName=$this->getLoginName();
        $identity->user_type=$this->user_type;
        $identity->profile=$this->profile;
        $identity->profileName=$this->getDisplayName();
        return $identity;
    }

    public function loginSuccess(){
        $this->tsLastLogin=time();
        //clearing the password reset fields if they are set
        /*$userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
          unset($this->profile->$userProfileFieldNameIDArr['new_password']);
          unset($this->profile->$userProfileFieldNameIDArr['new_password_ts']);
          unset($this->profile->$userProfileFieldNameIDArr['new_password_key']);*/

        $this->save();
        $message=sprintf('Successful login attempt for user %s',$this->profileName);
        $logger= Zend_Registry::get('logEvent');
        $logger->notice($message,1);
    }

    public function generateSessionToken($deviceId=NULL,$version=NULL,$lat=NULL,$lon=NULL,$deviceVersion=NULL,$deviceToken=NULL){
        $sid = SavCo_FunctionsAdmin::CreateToken('api');
        if($sid){ //Place so only modified in user
            $this->session->load($this->getId()); //uses old session or cerate new one
            $this->session->user_id=($this->getId());
            $this->session->sessionId=$sid;
            $this->session->session_type=1;
            $this->session->device_id=(int)$deviceId;
            if($deviceVersion)$this->session->device_version=$deviceVersion;
            if($deviceToken)$this->session->device_token=$deviceToken;
            if($version)$this->session->version=$version;
            $this->session->lat=$lat;
            $this->session->lon=$lon;
            $this->session->tsCreated=time();
            $this->session->tsModified=NULL;
            $this->session->save();
        }
    }

    public function registrationSuccess(){
        //$this->tsLastLogin=time();
        $this->tsLastLogin=NULL;
        $this->save();

        $message=sprintf('Successful registration for user %s',$this->fbId);
        $logger= Zend_Registry::get('logEvent');
        $logger->notice($message,1);
    }

    public function retrievePassword(){
        if (!$this->isSaved())
            return false;

        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        // generate new password properties
        $this->tempArr['_newPassword']=SavCo_FunctionsGen::createRandomPassword(8);

        $this->profile->$userProfileFieldNameIDArr['new_password'] = md5($this->tempArr['_newPassword']);
        $this->profile->$userProfileFieldNameIDArr['new_password_ts'] = time();
        $this->profile->$userProfileFieldNameIDArr['new_password_key'] = md5(uniqid() .
            $this->getId() .
            $this->tempArr['_newPassword']);

        // save new password to profile and send e-mail
        $this->profile->save();

        $dataArr['user']=$this;
        $dataArr['tempArr']=$this->tempArr;
        $this->sendEmail('user-retrieve-password.tpl',$dataArr);

        $message=sprintf('Retrieving password for %s',$this->fbId);
        $logger= Zend_Registry::get('logEvent');
        $logger->notice($message,1);

        return true;
    }

    public function reqValidatePhone($phone10Info){
        if (!$this->isSaved())
            return false;

        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        // generate new password properties
        $this->tempArr['_newPassword']=SavCo_FunctionsGen::createRandomPassword(4);

        $this->profile->$userProfileFieldNameIDArr['new_phone10'] = $phone10Info['new_phone10'];
        $this->profile->$userProfileFieldNameIDArr['new_phone10Carrier_id'] = $phone10Info['new_phone10Carrier_id'];
        $this->profile->$userProfileFieldNameIDArr['new_phone10_ts'] = time();
        $this->profile->$userProfileFieldNameIDArr['new_phone10_key'] = $this->tempArr['_newPassword'];

        // save new password to profile and send e-mail
        $this->profile->save();
        $this->sendSms('user-validatePhone.tpl',array(),true);

        $message=sprintf('Validating sms phone number for %s',$this->fbId);
        $logger= Zend_Registry::get('logEvent');
        $logger->notice($message,1);

        return true;
    }

    public function validateNewPhone10($key)
    {
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        // check that valid password reset data is set
        if (!isset($this->profile->$userProfileFieldNameIDArr['new_phone10'])
            || !isset($this->profile->$userProfileFieldNameIDArr['new_phone10Carrier_id'])
            || !isset($this->profile->$userProfileFieldNameIDArr['new_phone10_ts'])
            || !isset($this->profile->$userProfileFieldNameIDArr['new_phone10_key'])) {

            return false;
        }

        // check if the password is being confirm within a 5 minutes
        if (time() - $this->profile->$userProfileFieldNameIDArr['new_password_ts'] > 300)
            return false;

        // check that the key is correct
        if ($this->profile->$userProfileFieldNameIDArr['new_password_key'] != $key)
            return false;

        // everything is valid, now update the account to use the new password

        // set the phone 10
        $this->profile->$userProfileFieldNameIDArr['phone10=']=$this->profile->$userProfileFieldNameIDArr['new_phone10'];
        $this->profile->$userProfileFieldNameIDArr['phone10Carrier_id']=$this->profile->$userProfileFieldNameIDArr['new_phone10Carrier_id'];

        unset($this->profile->$userProfileFieldNameIDArr['new_phone10']);
        unset($this->profile->$userProfileFieldNameIDArr['new_phone10Carrier_id']);
        unset($this->profile->$userProfileFieldNameIDArr['new_phone10_ts']);
        unset($this->profile->$userProfileFieldNameIDArr['new_phone10_key']);
        // finally, save the updated user record and the updated profile
        return $this->save();
    }

    public function confirmNewPassword($key)
    {
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        // check that valid password reset data is set
        if (!isset($this->profile->$userProfileFieldNameIDArr['new_password'])
            || !isset($this->profile->$userProfileFieldNameIDArr['new_password_ts'])
            || !isset($this->profile->$userProfileFieldNameIDArr['new_password_key'])) {

            return false;
        }

        // check if the password is being confirm within a day
        if (time() - $this->profile->$userProfileFieldNameIDArr['new_password_ts'] > 86400)
            return false;

        // check that the key is correct
        if ($this->profile->$userProfileFieldNameIDArr['new_password_key'] != $key)
            return false;

        // everything is valid, now update the account to use the new password

        // bypass the local setter as new_password is already an md5
        parent::__set('password', $this->profile->$userProfileFieldNameIDArr['new_password']);

        unset($this->profile->$userProfileFieldNameIDArr['new_password']);
        unset($this->profile->$userProfileFieldNameIDArr['new_password_ts']);
        unset($this->profile->$userProfileFieldNameIDArr['new_password_key']);

        // finally, save the updated user record and the updated profile
        return $this->save();
    }

    public function getLoginName(){
        //If first name is Set then Return
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        if (strlen($this->profile->$userProfileFieldNameIDArr['firstName'])>0){
            return $this->profile->$userProfileFieldNameIDArr['firstName'];
        }else{
            return $this->fbId;
        }
    }

    private function getDisplayName(){
        return $this->profileName;
    }
    public function getCanvasStarName(){  //Required location
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        if (strlen($this->profileName)>0){
            return '*'.$this->profileName;
        }else{
            return $this->getId();
        }
    }

    public function getProfileName(){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        if (strlen($this->profileName)>0){
            return '*'.$this->profileName;
        }else{
            return 'Sponttee'.$this->getId();
        }
    }

    static public function LoginFailure($fbId,$code=''){
        switch($code){
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                $reason='Unknown fbId';
                break;
            case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
                $reason='Multiple user found with this fbId. How did this happen!?!';
                break;
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                $reason='Invalid password';
                break;
            default:
                $reason='';
        }
        $message= sprintf('Failed login attempt for user %s',$fbId);

        if (strlen($reason)>0){
            $message.=sprintf('(%s)',$reason);
        }
        $logger = Zend_Registry::get('logEvent');
        $logger->warn($message,1);
    }

    public function getUserProfileDataArr(){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        $userProfile['firstName']=$this->profile->$userProfileFieldNameIDArr['firstName'];
        $userProfile['middleName']=$this->profile->$userProfileFieldNameIDArr['middleName'];
        $userProfile['lastName']=$this->profile->$userProfileFieldNameIDArr['lastName'];
        $userProfile['gender']=$this->profile->$userProfileFieldNameIDArr['gender'];
        $userProfile['profileName']=$this->profileName;
        $userProfile['aboutMe']=$this->profile->$userProfileFieldNameIDArr['aboutMe'];
        $userProfile['msg']=$this->getGlobalMsg();

        return $userProfile;
    }



    public function getGlobalMsg(){
        $msg['message']='You have 20 new spontts since your last visit';
        $msg['type']='success';
        $msg['num']='0';
        return $msg;
    }


    public function getSetLocation(){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        return $this->profile->$userProfileFieldNameIDArr['setLocation'];

    }

    public function setCurrentLocation($locationID){
        if ($locationID){
            $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
            $this->profile->$userProfileFieldNameIDArr['currentLocationID']=$locationID;
            $this->profile->$userProfileFieldNameIDArr['currentLocationID_ts']=time();
            $location= new DatabaseObject_UserLocation($this->getDb());

            $location->load($locationID);
            $this->updateLatLon($location->user_lat,$location->user_lon,2);
            return true;
        }else{
            return false;
        }
    }

    public function updateLatLon($lat,$lon,$geoDevice_id=0){
        $presence= new DatabaseObject_UserPresence($this->getDb()); //TODO: Make this MongoDB
        $presence->user_lat=$lat;
        $presence->user_lon=$lon;
        $presence->geoDevice_id=$geoDevice_id;
        //add this as the users presence
        $this->addPresence($presence);
    }



    //Adding
    public function addBet(DatabaseObject_Bet $bet,$betNotes='',$betExpiry,DatabaseObject_Prize $prize=null,$prizeAmt=0,$prizeNotes='',$teamSelectedId=0){
         $aUserBet= new DatabaseObject_UserBet($this->getDb());
         $aUserBet->user_id=$this->getId();
         $aUserBet->bet_id= $bet->getId();
         $aUserBet->bet_notes=$betNotes;
         $aUserBet->bet_expiry=$betExpiry;
         $aUserBet->prize_id=$prize?$prize->getId():0;
         $aUserBet->prize_amt=$prizeAmt;
         $aUserBet->prize_notes=$prizeNotes;
         $aUserBet->team_id=(int)$teamSelectedId; //Can also verify that the team exists
         $aUserBet->save();
         return $aUserBet;
    }

    public function inviteFriendsForUserBetWithFBIds(DatabaseObject_UserBet $userBet,$fbIds){
        //Use auth to invite Friend
        $config= Zend_Registry::get('config');
        $profileEnum=$this->profileEnum;
        $accessToken=$this->profile->$profileEnum['fb_accessToken'];
        // $facebook_id=Zend_Registry::get('config')->facebook->appid;
        //create message with token gained before

        if($accessToken){
            $facebook= new Facebook($config);
            $facebook->setAccessToken($accessToken);
            // get a list of your your friends'
            $friends = $facebook->api('/me/friends?access_token='.$accessToken.'&fields=id');
            // condense those IDs into a comma-separated string
            $friends_ids = implode(',', $friends);
            // now query for friends' name, hometown and location
            $friends_info = $facebook->api('/?access_token='.$accessToken.'&fields=id,name,hometown,location&ids='.$friends_ids);
        }



    }

    public function addPresence(DatabaseObject_UserPresence $newPresence){
        //Needs to be tied to a user always and better control over presence being added
        //Count right here to decide whether to pop and delete
        //More like presence management- if no true lat and lon comes in then data is not recorded
        //report that data is not being updated for user to know though
        //Look at better way to handle this.
        if (abs($newPresence->user_lat)==0 && abs($newPresence->user_lon)==0){
            return;
        }
        //Check that presence has changed- if so then update-otherwise just update time

        $lastPresence= $this->presences[count($this->presences)-1];
        if (($lastPresence->user_lat != $newPresence->user_lat) &&	($lastPresence->user_lon != $newPresence->user_lon)){
            if(count($this->presences)>=self::MAX_PRESENCE_HISTORY){
                //should never be greater than MAX_PRESENCE_HISTORY
                //Log that this is being popped
                //print_r($this->presences);
                /*foreach($this->presences as $presence){
                        //echo $presence->getId().'<br/>';
                    }*/

                $deletePresence= new DatabaseObject_UserPresence($this->getDb());
                $deleteCopy=array_pop($this->presences);
                //echo "The deleted id is".$deleteCopy->getId();- place in log
                $deletePresence->load($deleteCopy->getId());
                $deletePresence->delete();
            }
            if (!$newPresence->addressJSON){
                //create an address for this
                //reverse geocode
                $geoAddress=SavCo_Geocoder::GetAddressInfo($newPresence->user_lat,$newPresence->user_lon);
                $newPresence->addressJSON=Zend_Json::encode($geoAddress);
            }

            $newPresence->tsLastUpdate=time();
            $newPresence->user_id=$this->getId();
            $newPresence->save();
            //Now add to top of array
            $this->presences=array_merge(array($newPresence),$this->presences);
        }else{
            //just update the time
            $lastPresence->tsLastUpdate=time();
            $lastPresence->save();
        }
    }


    public function fbStatusUpdate($statusToPost,$facebookHook){
        //update user's status using graph api
        if (strlen($statusToPost)>0){
            try {
                $statusUpdate = $facebookHook->api('/me/feed', 'post', array('message'=>$statusToPost, 'cb' => ''));
            } catch (FacebookApiException $e) {
                d($e);
            }
        }

    }

    public function fbWallUpdate($wallPost,$facebook){
        $facebook_id=Zend_Registry::get('config')->facebook->hydra->appid;
        //create message with token gained before
        $facebook->setAccessToken($this->fbAccessToken);

        //$post = array('access_token' =>$this->fbAccessToken,
        // 'message' => $wallPost);
        $post=$wallPost;
        //and make the request
        $res = $facebook -> api('/me/feed', 'POST', $post);
        //print($res);
    }

    public function fbPublishToPromotersAndUserWalls(DatabaseObject_UserCampaign $theCampaign,$config,Facebook_Access $facebook){
        //If this is my campaign then for each promoter with yes .. Publish
        $campaignApprovals=$theCampaign->approvedIds;
        if(count($campaignApprovals)>0){
            $wallPost=$facebook->buildPostWithCampaign($theCampaign,$config);
            $this->fbWallUpdate($wallPost,$facebook);
            foreach ($campaignApprovals as $anApprovedResponse) {
                $anApprovedResponse->tsPublished=time();
                $anApprovedResponse->save();

                $aPromoter=new DatabaseObject_User($this->getDb());
                $aPromoter->load($anApprovedResponse->user_id);
                $aPromoter->fbWallUpdate($wallPost,$facebook);
            }
        }
        //Set Post Date
        $theCampaign->tsPublished=time();
        $theCampaign->save();
        return true;
    }


    public function isValidCurrentLocation(){
        return true;

    }

    public static function GetCanvasName($user_id=0){
        $db=SavCo_ConstantArr::getDbase();
        $select = 'SELECT profileName FROM users WHERE ';
        $select .='user_id='.$user_id.' AND profileName IS NOT NULL LIMIT 1';
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $profileName=strlen($row['profileName'])>0?'*'.$row['profileName']:$user_id;
            }
        }else{
            $profileName=$user_id;
        }
        return $profileName;
    }
    public static function GetProfileNameByUserId($user_id){
        $db=SavCo_ConstantArr::getDbase();
        $select = 'SELECT profileName FROM users WHERE ';
        $select .='user_id='.$user_id.' AND profileName IS NOT NULL LIMIT 1';
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $profileName=strlen($row['profileName'])>0?'*'.$row['profileName']:$user_id;
            }
        }else{
            $profileName="Sponttee$user_id";
        }
        return $profileName;
    }



    public static function GetUserName($user_id=0){
        $db=SavCo_ConstantArr::getDbase();
        $select = 'SELECT profile_value FROM users_profile WHERE ';
        $select .='user_id='.$user_id.' AND profile_key="up1" LIMIT 1';
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $userName=$row['profile_value'];
            }
        }else{
            $userName='Sponttee '.$user_id;
        }
        return $userName;
    }


    public static function GetProfileImageId($user_id=0){
        $imageid=0; //default imageid;
        $db=SavCo_ConstantArr::getDbase();
        $select = 'SELECT image_id FROM users_images WHERE ';
        $select .='user_id='.$user_id.' ORDER BY image_id DESC LIMIT 1';
        //print $select;
        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $imageid=$row['image_id'];
            }
        }
        return $imageid;
    }

    public static function GetProfileImage($user_id=0){
        $db=SavCo_ConstantArr::getDbase();
        $profileImageId=self::GetProfileImageId($user_id);

        $user_image=new DatabaseObject_File_Image_User($db);
        $user_image->load($profileImageId);

        return $user_image;
    }

    protected function sendEmail($tpl,$dataArr=array(),$html=false,$email=""){
        $fromName="no-reply";
        $emailFrom=Zend_Registry::get('config')->email->from->email;
        $emailTo=strlen($email)>0?$email:$this->usernameEmail;
        if(SavCo_ZendExtend::Email_FromName_From_To_Data_Template_isHTML($fromName,$emailFrom,$emailTo,$dataArr,$tpl,$html)){
            //echo "Email has been sent";
        }
    }

    public function sendSms($tpl,$dataArr=array(),$newPhone=false){
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();

        if (!$newPhone){
            if (strlen($this->$userProfileFieldNameIDArr['phone10'])>0&&
                strlen($this->$userProfileFieldNameIDArr['phone10Carrier_id'])>0){
                $carrier=new DatabaseObject__carrier($this->_db);
                $phoneMail=$this->$userProfileFieldNameIDArr['phone10'].$carrier->emailext;
            }else{
                $message=sprintf('No SMS- Not able to find a phone 10 for user ',$this->user_id);
                $logger= Zend_Registry::get('logEvent');
                $logger->warn($message,1);
                return;
            }
        }else{
            if (strlen($this->$userProfileFieldNameIDArr['new_phone10'])>0&&
                strlen($this->$userProfileFieldNameIDArr['new_phone10Carrier_id'])>0){
                $carrier=new DatabaseObject__carrier($this->_db);
                $phoneMail=$this->$userProfileFieldNameIDArr['new_phone10'].$carrier->emailext;
            }else{
                $message=sprintf('No SMS- Not able to find a new phone for user ',$this->user_id);
                $logger= Zend_Registry::get('logEvent');
                $logger->warn($message,1);
                return;
            }
        }

        if (strlen($phoneMail)>0){
            //Goes if old phone or setting new phone

            $templater= new Templater();
            $templater->user=$this;
            $templater->tempArr=$this->tempArr;
            $templater->dataArr=$dataArr;

            $templater->userProfileFieldNameIDArr;
            //fetch the email body
            $body= $templater->render('email/sms/'.$tpl);

            //extract the subject from the first line.
            list($subject,$body)=preg_split('/\r|\n/',$body,2);

            //now set-up and send the e-mail
            $mail= new Zend_Mail();

            //set the phone number
            $mail->addTo($phoneMail);

            //get the admin 'from details from the config
            $mail->setFrom(Zend_Registry::get('config')->email->from->email,
                Zend_Registry::get('config')->email->from->email);

            //set the subject and body and send the mail
            $mail->setSubject(trim($subject));
            $mail->setBodyText(trim($body));
            $mail->send();
        }
        else{
            $message=sprintf('Not able to email user- no email address',$this->user_id);
            $logger= Zend_Registry::get('logEvent');
            $logger->warn($message,1);
        }
    }

    public  function profileImage(){
        $aProfileImage= new DatabaseObject_File_Image_User($this);
        $profileEnum=$this->profileEnum;
        $aProfileImage->load($this->profile->$profileEnum['profilePicImageId']);
        //even if it is null - return- imagefilename needs object type to make default

        return $aProfileImage;
    }


    public static function GetTotal($db){ //change the logic to a simple count
        $select = sprintf('select %s from %s',
            DatabaseObject_User::$constId,
            DatabaseObject_User::$constTable);

        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        return  count($rowset);
    }

    public function myBetsArrayRepresentationForFilterType($filterType,$config,$limit=20,$offset=0,$lastId=0,$lat=0.0,$lon=0.0){
        $bets=array();
        //$betsTable=DatabaseObject_Bet::$constTable;
        //$betsFullId=sprintf("%s.%s",DatabaseObject_Bet::$constId);
        $userId=$this->getId();
        $select = $this->getDb()->select();
        $select->from('bets',array("bet_id","bettype_id","bet_description","game_starts","game_state_id"));
        $select->joinLeft("users_bets","bets.bet_id=users_bets.bet_id",array("users_bets.ub_id","users_bets.tsCreated"));
        $select->joinLeft("users_bets_invites","users_bets.ub_id=users_bets_invites.ub_id",array("users_bets_invites.user_id as bettee","users_bets_invites.won as won"));
        $select->where("users_bets.user_id=$userId OR (users_bets_invites.user_id=$userId  AND users_bets_invites.response_type=1)");
        $select->group('users_bets.ub_id');
        switch($filterType){
            case 'nfl':
                $select->where("bets.bettype_id =1");
                break;
            case 'nba':
                $select->where("bets.bettype_id =2");
                break;
            case 'mlb':
                $select->where("bets.bettype_id =3");
                break;
            case 'nhl':
                $select->where("bets.bettype_id =4");
                break;
            case 'golf':
                $select->where("bets.bettype_id =5");
                break;
            default:
                //returns
                break;
        }
        $stmt=$this->getDb()->query($select);
        $bets=$stmt->fetchAll();

        //Loop To Get Associated Teams
        $fullBets=array();
        foreach($bets as $aBet){
            $teams=DatabaseObject_BetTeam::TeamsArrayRepresentationForBetId($this->getDb(),$aBet['bet_id']);

            if($aBet['won']!=NULL){
                $aBet['game_starts']=$aBet['won']==1?"won":"lost";
            }


            $aBet['teams']=$teams;
            $fullBets[]=$aBet;
        }

        return $fullBets;
    }


    public function myBetsCustomArrayRepresentationForFilterType($filterType,$config,$limit=20,$offset=0,$lastId=0,$lat=0.0,$lon=0.0){
       //Don't change anything in here unless you really know what you are doing!!!!
        $bets=array();
        $userId=$this->getId();
        $select = $this->getDb()->select();
        $select->from('custombets',array("_betcustom_id as bet_id","bettype_id","bet_description","game_starts","game_state_id"));
        $select->joinLeft("users_bets","custombets._betcustom_id=users_bets.bet_id",array("users_bets.ub_id","users_bets.tsCreated"));
        $select->joinLeft("users_bets_invites","users_bets.ub_id=users_bets_invites.ub_id",array("users_bets_invites.user_id as bettee"));
        $select->where("users_bets.user_id=$userId OR (users_bets_invites.user_id=$userId  AND users_bets_invites.response_type=1)");
        $select->group('users_bets.ub_id');   //Fix to an issue where multiple user_bet data was showing up
        //$select= "SELECT * from custombets INNER JOIN users_bets ON custombets._betcustom_id=users_bets.bet_id";


        switch($filterType){
            case 'golf': //Only Custom Game Pressent
                $select->where("bets.bettype_id =5");
                break;
            default:
                //returns
            break;
        }
        //order
        $select->order("users_bets.tsCreated DESC");

        $stmt=$this->getDb()->query($select);
        $bets=$stmt->fetchAll();

        //TEAMS- Custom Bets do not presently have teams

        //Loop To Get Associated Teams
        $fullBets=array();
        foreach($bets as $aBet){
            //$aBet['bet_id']=sprintf("1.%s",$aBet['bet_id']); //Fix Up for Custom since it begins with 1- whole number
            $aBet['teams']=array();
            $fullBets[]=$aBet;
        }

        if($aBet['won']!=NULL){
            $aBet['game_starts']=$aBet['won']==1?"won":"lost";
        }

        return $fullBets;
    }


    public function betsCount(){
        $select =sprintf("SELECT count(*) FROM `users_bets` WHERE `users_bets`.`user_id` = %d",$this->getId());
        $stmt=$this->getDb()->query($select);
        $number_of_rows=$stmt->fetchColumn();

        return (int)$number_of_rows;
    }

    public function addPointsForType($pointType){
        //Can set up point system here
        //Creating bet
        //answer invite
        $points=0;
        switch($pointType){
            case 'create_bet':
                $points=20;
            break;
            case 'accept_invite':
                $points=10;
            break;
            case 'win':
                $points=20;
            break;
            case 'lost':
                $points=10;
             break;
            default:
                $points=1;
            break;
        }
        $this->points=(int)$this->points+$points;
        $this->save();

        $this->adjustLevels();
        SavCo_ZendExtend::Log(sprintf(':USER:%d:POINTS:%d:TYPE:%s',$this->getId(),$points,$pointType),'notice');

    }

    public static function UsersArrayRepresentationForFilterType(DatabaseObject $user,$filterType,$config,$limit=10,$offset=0,$lastId=0,$lat='0.0',$lon='0.0'){
        //points,bucks,friendpoints,friendbucks
        $bets=array();
        $userTable=DatabaseObject_User::$constTable;

        if(strcmp($filterType,'nearby')!=0){
            $select = $user->getDb()->select();
            $select->from($userTable,"*");

            $select->limit((int)$limit,(int)$offset);
            switch($filterType){
                case 'points':
                    $select->order("points DESC");
                break;
                case 'bucks':
                    $select->order("bucks DESC");
                break;
                case 'friendpoints':
                    $select->order("points DESC");
                break;
                case 'friendbucks':
                    $select->order("bucks DESC");
                break;
                default:
                    $select->where("$userTable.user_id =".$user->getId());
                break;
            }

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

        $stmt=$user->getDb()->query($select);
        $data=$stmt->fetchAll();

      return $data;
    }


    public function pushIOSAlertWithMessageBadgeCountAndSound($message,$pony=NULL, $badgeCount=NULL, $soundName='default'){
            $config= Zend_Registry::get('config');
            $pushNotification= new SavCo_PushIOS($config);
            if(!$result=$pushNotification->pushSimpleNotification($this,$message,$pony,(int)$badgeCount,$soundName)){
                $msg=sprintf('Could not push to user with id %d ', $this->getId());
                SavCo_ZendExtend::Log($msg,'warning');
            }
    }

    public function fbAppUsersFriendIds($config){
      /*  $facebook= new Facebook_Access(array(
                        'appId'  => $config->facebook->appid,
                        'secret' => $config->facebook->secret,
        ));
        $token=$facebook->getAccessToken();

       //$facebook->require_login();
        //$facebook->require_frame();


        //$fql = sprintf("Select name, uid, pic_small from user where is_app_user = 1 and uid in (select uid2 from friend where uid1 = %s) order by concat(first_name,last_name) asc",$this->fbId);
        $fql = "Select name, uid, pic_small from user where is_app_user = 1 and uid in (select uid2 from friend where uid1 = me()) order by concat(first_name,last_name) asc";
        $ret = $facebook->api(array(
            'method' => 'fql.query',
                'query' => "$fql",
            ));
        return $ret;*/
    }

    public function winlosses(){
       return  DatabaseObject_UserBet::WinLossesForUser($this);
    }

    /*See Above Chart
     *
     *
     */
    public function adjustLevels(){
        $oldLevel=$this->level;
        if($this->points <0){
            $this->level=0;
        }elseif( 0 <= $this->points &&  $this->points<=100){
            $this->level=1;
        }elseif( 101 <= $this->points &&  $this->points<=200){
            $this->level=2;
        }elseif( 201 <= $this->points &&  $this->points<=300){
            $this->level=3;
        }elseif( 301 <= $this->points &&  $this->points<=400){
            $this->level=4;
        }elseif( 400 <= $this->points &&  $this->points<=900){
            $this->level=5;
        }elseif( 901 <= $this->points &&  $this->points<=1000){
            $this->level=6;
        }elseif( 1001 <= $this->points){
            $this->level=7;
        }
        if($oldLevel!=$this->level){
            $this->save();
        }
    }

}
