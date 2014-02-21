<?
class FormProcessor_UserChangePassword extends FormProcessor
{
    protected $db = null;
    public $user = null;

    protected $_validateOnly = false;
    protected $_apiLogin = false;
    private $_currentPassword=null;
    private $_newPassword=null;
    private $_confirmPassword=null;

    public function __construct(Zend_Db_Adapter_Abstract $db, DatabaseObject_User $user)
    {
        parent::__construct();
        $this->db = $db;
        $this->user=$user;
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
            $this->_currentPassword=property_exists($phpNative, "currentPassword")?$phpNative->currentPassword:null;
            $this->_newPassword=property_exists($phpNative, "newPassword")?$phpNative->newPassword:null;
            $this->_confirmPassword=property_exists($phpNative, "confirmPassword")?$phpNative->confirmPassword:null;
        }else{
            $this->addError('form','This is presently only for webforms.');
        }

        //Password is
        if (strlen($this->_currentPassword) == 0)
                $this->addError('currentPassword','Current Password is required');

        if (strlen($this->_newPassword) == 0)
                $this->addError('newPassword','New Password is required');

        if (strlen($this->_confirmPassword) == 0)
                $this->addError('confirmPassword','You must confirm the password');

        //Password
        if (!$this->hasError()){
             if ($this->_newPassword != $this->_confirmPassword){
                $this->addError('confirmPassword', 'Confirmed password does not match new password');
               // $this->addError('confirmPassword', '');
            }

            $newWord= md5($this->_currentPassword);
            $oldWord=$this->user->password;
            if (md5($this->_currentPassword)!= $this->user->password){
                $this->addError('currentPassword', 'Sorry, this is not your current password.');
            }
        }

        if (!$this->hasError()){
            $this->user->password=$this->_newPassword;
            $this->user->save();
        }

        return !$this->hasError();
   }

}

?> 