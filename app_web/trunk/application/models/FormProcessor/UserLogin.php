<?
class FormProcessor_UserLogin extends FormProcessor
{
    protected $db = null;
    public  $user = null;
    protected $_validateOnly = false;
    protected $_apiLogin = false;
    protected $_clearIdentity =true;
    private $_usernameEmail=null;
    private $_password=null;
    private $_device_id=null;
    private $_device_version=null;
    private $_device_token=null;
    private $_version=null;
    private $_lat=null;
    private $_lon=null;

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
            $phpNative = Zend_Json::decode(stripslashes($loginJSON), Zend_Json::TYPE_OBJECT);
            $this->_usernameEmail=property_exists($phpNative, "usernameEmail")?$phpNative->usernameEmail:null;
            $this->_password=property_exists($phpNative, "password")?$phpNative->password:null;
            $this->_device_id=property_exists($phpNative, "device_id")?$phpNative->device_id:null;
            $this->_device_version=property_exists($phpNative, "device_version")?$phpNative->device_version:null;
            $this->_device_token=property_exists($phpNative, "device_token")?$phpNative->device_token:null;
            $this->_version=property_exists($phpNative, "version")?$phpNative->version:null;
            $this->_lat=property_exists($phpNative, "lat")?$phpNative->lat:null;
            $this->_lon=property_exists($phpNative, "lon")?$phpNative->lon:null;
        }else{
            if ($this->_apiLogin){
                $this->_usernameEmail=$request->getParam('usernameEmail');
                $this->_password=$request->getParam('password');
                $this->_device_id=$request->getParam('device_id');
                $this->_device_version=$request->getParam('device_version');
                $this->_device_token=$request->getParam('device_token');
                $this->_version=$request->getParam('version');
                $this->_lat=$request->getParam('lat');
                $this->_lon=$request->getParam('lon');
            }else{
                $this->_usernameEmail=$request->getParam('usernameEmail');
                $this->_password=$request->getParam('password');
                $this->_device_id=$request->getParam('device_id');
                $this->_device_version=$request->getParam('device_version');
                $this->_device_token=$request->getParam('device_token');
                $this->_version=$request->getParam('version');
                $this->_lat=$request->getParam('lat');
                $this->_lon=$request->getParam('lon');
            }
        }

        if (strlen($this->_usernameEmail) == 0)$this->addError('usernameEmail','Login Email is a Required Field');
        if (strlen($this->_password) == 0)$this->addError('password', 'Password  is a Required Field');


        if (!$this->hasError()) {
            // setup the authentication adapter
            $validUserId=null;

            $validUserId=$this->userId_simpleAuthWithUsernameEmailPassword($this->_usernameEmail,$this->_password);

            if ($validUserId && !$this->hasError()){   //VALID RESULT
                if(!$this->_validateOnly){
                    $user = new DatabaseObject_User($this->db);
                    $user->load($validUserId);
                    $user->loginSuccess();

                    if(!$this->_apiLogin){ //replace Identity here
                        $auth = Zend_Auth::getInstance();
                        $identity = $user->createAuthIdentity();
                        $auth->getStorage()->write($identity);

                        if($this->remeberme=='on'){
                            $seconds=60*60*24*14;//14 days
                            Zend_Session::rememberMe($seconds);
                        }else{
                            Zend_Session::forgetMe();
                        }

                    }else{
                        $this->user=$user;  //set session data here
                        $user->generateSessionToken($this->_device_id,$this->_version_id,$this->_lat,$this->_lon);
                    }
                }else{
                    $user = new DatabaseObject_User($this->db);
                    $user->load($validUserId);
                    $user->loginSuccess();
                }
            }else{
                if (!$this->hasError() && !$validUserId){
                    $this->addError('loginDetails', 'Your Login details are invalid');
                }
            }
            return !$this->hasError();
        }
    }

    protected function userId_simpleAuthWithUsernameEmailPassword($usernameEmail='',$password=''){
        $identityField='';
        $userId=null;
        if (strpos($this->_usernameEmail,'@')>0){
            $identityField='usernameEmail';
        }else{
            $identityField='profileName';
        }
        $md5Pass=md5($password);
        $select ="SELECT user_id,expiry FROM users WHERE $identityField = '$usernameEmail' AND  password ='$md5Pass'";

        $stmt=$this->db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)>0){
            foreach($rowset as $row){
                $userId=$row['user_id'];

                if ($row['expiry']<=time() && $row['expiry']!=null){
                    $this->addError("expiry","Sorry, your account has expired. Please see adminstrator.");
                }
            }
        }else{
            DatabaseObject_User::LoginFailure($this->_usernameEmail,"Error logging in via API");
        }
        return $userId;
    }

}

?>