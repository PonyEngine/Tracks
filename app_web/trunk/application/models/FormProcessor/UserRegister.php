<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 12/29/13
 * Time: 11:34 AM
 * To change this template use File | Settings | File Templates.
 */
class FormProcessor_UserRegister extends FormProcessor
{
    protected $db = null;
    public $user = null;
    public $filePath=null;
    protected $_validateOnly=false;
    protected $_apiSignup=false;
    protected $_clearIdentity=true;
    private $_fullName=null;
    private $_usernameEmail=null;
    private $_password=null;
    private $_profileName=null;
    private $_phone10=null;
    private $_device_id=null;
    private $_version_id=null;
    private $_lat=null;
    private $_lon=null;


    public function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
        $this->user = new DatabaseObject_User($db);
        $this->user->userType ='newMember';
    }

    public function apiRegister($flag){
        $this->_apiSignup=(bool)$flag;
        $this->_clearIdentity=(bool)!$flag;
    }

    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }

    public function process(Zend_Controller_Request_Abstract $request,$isPost=true)
    {
        $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        if($registerJSON=$request->getPost('json')){
            $phpNativeLogin = Zend_Json::decode($registerJSON, Zend_Json::TYPE_OBJECT);
            $this->_fullName=$this->sanitize($phpNativeLogin->fullname);
            $this->_usernameEmail=$this->sanitize($phpNativeLogin->email);
            $this->_password=$this->sanitize($phpNativeLogin->password);
            $this->_profileName=$this->sanitize($phpNativeLogin->username);
            $this->_phone10=$this->sanitize($phpNativeLogin->phone);
            $this->_device_id=$this->sanitize($phpNativeLogin->device_id);
            $this->_version_id=$this->sanitize($phpNativeLogin->version_id);
            $this->_lat=$this->sanitize($phpNativeLogin->lat);
            $this->_lon=$this->sanitize($phpNativeLogin->lon);
        }else{
            $this->_fullName= $this->sanitize($request->getParam('fullname'));
            $this->_usernameEmail= $this->sanitize($request->getParam('email'));
            $this->_password=$this->sanitize($request->getParam('password'));
            $this->_profileName=$this->sanitize($request->getParam('username'));
            $this->_phone10=$this->sanitize($request->getPost('phone'));
            $this->_device_id=$this->sanitize($request->getPost('device_id'));
            $this->_version_id=$this->sanitize($request->getPost('version_id'));
            $this->_lat=$this->sanitize($request->getPost('lat'));
            $this->_lon=$this->sanitize($request->getPost('lon'));
        }

        // validate the e-mail address
        $validator =new Zend_Validate_EmailAddress();
        if (strlen($this->_usernameEmail)== 0){
            $this->addError('usernameEmail', 'Email address is required for joining.');
        }else if (!$validator->isValid($this->_usernameEmail)){
            $this->addError('usernameEmail', 'Invalid Email Address');
        }
        else if ( $this->user->usernameEmailExists($this->db,$this->_usernameEmail)){
            $this->addError('usernameEmail', 'This email is in use.');
        }
        else
            $this->user->usernameEmail= $this->_usernameEmail;

        //$this->user->usernameEmail= $this->_usernameEmail;
        //Password
        if (strlen($this->_password) == 0)
            $this->addError('password', 'Password is a required field.');
        else
            $this->user->password = $this->_password;

        //ProfileName
        if (strlen($this->_profileName) > 0){
            if (DatabaseObject_User::AvailProfileNameAndValid($this->db,$this->_profileName)){
                $this->user->profileName = $this->_profileName;
            }else{
                $this->addError('profileName', 'Username is not valid.');
            }
        }

        if (strlen($this->_fullName) < 1){
            $this->addError('fullName', 'Please enter your full name.');
        }

        //phonevalidation
        if (strlen($this->_phone10) > 0){//attempting to add phone number
            if(strlen($this->_phone10)!=10) $this->addError('phone10', 'Please enter a valid phone number.');    //TODO:Use true validation of phone
        }


        // if no errors have occurred, save the user
        if (!$this->_validateOnly && !$this->hasError()) {
            $profileEnum=$this->user->profileEnum;

            $this->_fullName=SavCo_FunctionsGen::GetNameParts($this->_fullName);
            if($this->_fullName['first'])$this->user->profile->$profileEnum['firstName']=$this->_fullName['first'];
            if($this->_fullName['last'])$this->user->profile->$profileEnum['lastName']=$this->_fullName['last'];
            if($this->_phone10){
                //Check if valid
                $this->user->profile->$profileEnum['phone10']=$this->_phone10;
            }
            //Also get the lat and lon position
            $this->user->profile->$userProfileFieldNameIDArr['ip'] = $_SERVER['REMOTE_ADDR'];

            try{
                $this->user->save();
                $isInternal=true;
                //Upload photo if available (Optional)- Do not let it hold back signing up
                /*
                $fp=new FormProcessor_File_Image_User($this->user,"userPhoto",$isInternal);
                if($fp->process($request)){
                    $fileInfo=$fp->fileInfoOfUploaded();
                    $this->image=$fp->image;

                    $aUserPhoto=new DatabaseObject_File_Image_User($this->user);

                    if($aUserPhoto->saveFile($fileInfo['filePath'],$isInternal)){//Save the Event Image Object
                        $this->user->profile->$profileEnum['profilePicImageId']=$aUserPhoto->getId();
                        $this->user->save();
                    }
                }else{
                    //Send Warning to the system about user's photo
                }*/

                if(!$this->_apiSignup){ //replace Identity here //Save Identity for login
                    $auth = Zend_Auth::getInstance();
                    $identity = $this->user->createAuthIdentity();
                    $auth->getStorage()->write($identity);

                    if($this->remeberme=='on'){
                        $seconds=60*60*24*14;//14 days
                        Zend_Session::rememberMe($seconds);
                    }else{
                        Zend_Session::forgetMe();
                    }

                }else{
                    $this->user->generateSessionToken($this->_device_id,$this->_version_id,$this->_lat,$this->_lon);
                }
            }catch(Exception $e){
                $this->addError('exception', $e->getMessage());
            }

        }

        // return true if no errors have occurred
        return !$this->hasError();
    }
}
?>