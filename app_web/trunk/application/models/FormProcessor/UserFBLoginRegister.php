<?
class FormProcessor_UserFBLoginRegister extends FormProcessor
{
    protected $db = null;
    public $user = null;
    protected $_validateOnly = false;
    protected $_apiLogin = false;
    protected $_clearIdentity =true;

    private $_fbId=null;
    private $_fbName=null;
    private $_fbFName=null;
    private $_fbMName=null;
    private $_fbLName=null;
    private $_fbEmail=null;
    private $_fbGender=null;
    private $_fbProfileURL=null;
    private $_fbPermissions=null;
    private $_fbUsername=null;
    private $_fbLocale=null;
    private $_authType=null;
    private $_device_id;
    private $_device_version=null;
    private $_device_token=null;
    private $_version=null;
    private $_lat;
    private $_lon;

    public function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
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
        if($loginJSON=$request->getPost('json')){
            $phpNativeLogin = Zend_Json::decode(stripslashes($loginJSON), Zend_Json::TYPE_OBJECT);
            $this->_usernameEmail=$phpNativeLogin->usernameEmail;
            //$this->_password=$phpNativeLogin->password;
        }else{
            $this->_fbId=$request->getParam('fbId');
            $this->_fbName=$request->getParam('fbName');
            $this->_fbFName=$request->getParam('fbFName');
            $this->_fbLName=$request->getParam('fbLName');
            $this->_fbEmail=$request->getParam('fbEmail');
            $this->_fbGender=$request->getParam('fbGender');
            $this->_fbProfileURL=urldecode($request->getParam('fbProfileURL'));
            $this->_fbPermissions=$request->getParam('fbPermissions');
            $this->_fbUsername=$request->getParam('fbUsername');
            $this->_fbLocale=$request->getParam('fbLocale');
            $this->_device_id=$request->getParam('device_id');
            $this->_device_version=$request->getParam('device_version');
            $this->_device_token=$request->getParam('device_token');
            $this->_version=$request->getParam('version');
            $this->_lat=$request->getParam('lat');
            $this->_lon=$request->getParam('lon');
        }


        if (strlen($this->_fbId) == 0)$this->addError('fbId', 'A Facebook Id is a Required Field');


        if (!$this->hasError()) {

            //Check to see if it exists
            $userObj= new DatabaseObject_User($this->db);

            if($userObj->loadByFbId((int)$this->_fbId)){
                //User already exists- update info and reeamil if address is different

                $profileEnum=$userObj->profileEnum;

                //Update
                if($this->_fbName)$userObj->profile->$profileEnum['firstName']=$this->_fbName;
                if($this->_fbFName)$userObj->profile->$profileEnum['firstName']=$this->_fbFName;
                if($this->_fbMName)$userObj->profile->$profileEnum['middleName']=$this->_fbMName;
                if($this->_fbLName)$userObj->profile->$profileEnum['lastName']=$this->_fbLName;

                //Email
                if($this->_fbEmail && strlen($this->_fbEmail)>0){
                    $userObj->usernameEmail=$this->_fbEmail;
                    $userObj->profile->$profileEnum['fb_email']=$this->_fbEmail;
                }else{
                    $userObj->usernameEmail="no-reply@ponyengine.com";
                }

                if($this->_fbUsername && strlen($this->_fbUsername)>0){
                    $userObj->profileName=$this->_fbUsername;
                    $userObj->profile->$profileEnum['fb_profilename']=$this->_fbUsername;
                }else{
                    // $userObj->profileName="";
                }

                if($this->_fbProfileURL)$userObj->profile->$profileEnum['fb_profileURL']=$this->_fbProfileURL;
                if($this->_fbPermissions)$userObj->profile->$profileEnum['fb_permissions']=$this->_fbPermissions;
                if($this->_fbLocale)$userObj->profile->$profileEnum['fb_locale']=$this->_fbLocale;

                $userObj->loginSuccess();
                $userObj->generateSessionToken($this->_device_id,$this->_version,$this->_lat,$this->_lon,$this->_device_version,$this->_device_token);
                $this->_authType='login';
            }else{
                //New User- Register and send email and generatesession
                $userObj->fbId=$this->_fbId;
                $userObj->password="facebookToken";
                $profileEnum=$userObj->profileEnum;
                if($this->_fbFName)$userObj->profile->$profileEnum['firstName']=$this->_fbFName;
                if($this->_fbMName)$userObj->profile->$profileEnum['middleName']=$this->_fbMName;
                if($this->_fbLName)$userObj->profile->$profileEnum['lastName']=$this->_fbLName;
                //Email
                if($this->_fbEmail && strlen($this->_fbEmail)>0){
                    $userObj->usernameEmail=$this->_fbEmail;
                    $userObj->profile->$profileEnum['fb_email']=$this->_fbEmail;
                }else{
                    $userObj->usernameEmail="no-reply@ponyengine.com";
                }
                if($this->_fbUsername && strlen($this->_fbUsername)>0){
                    $userObj->profileName=$this->_fbUsername;
                    $userObj->profile->$profileEnum['fb_profilename']=$this->_fbUsername;
                }else{
                   // $userObj->profileName="";
                }
                if($this->_fbName)$userObj->profile->$profileEnum['fb_name']=$this->_fbName;
                if($this->_fbProfileURL)$userObj->profile->$profileEnum['fb_profileURL']=$this->_fbProfileURL;
                if($this->_fbPermissions)$userObj->profile->$profileEnum['fb_permissions']=$this->_fbPermissions;
                if($this->_fbLocale)$userObj->profile->$profileEnum['fb_locale']=$this->_fbLocale;
                $userObj->registrationSuccess();
                $userObj->generateSessionToken($this->_device_id,$this->_version,$this->_lat,$this->_lon,$this->_device_version,$this->_device_token);
                $this->_authType="register";
            }
            $userObj->save();
            $this->user=$userObj;

        }
        return !$this->hasError();
  }

    public function authType(){
        return $this->_authType;
    }
}