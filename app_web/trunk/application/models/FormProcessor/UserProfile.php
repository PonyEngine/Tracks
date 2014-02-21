<?
class FormProcessor_UserProfile extends FormProcessor
{
    protected $db = null;
    public $user = null;
    protected $_fullName;
    protected $_firstName;
    protected $_lastName;
    protected $_usernameEmail;
    protected $_phone10;
    //protected $_profileTmpImgPath;

    public function __construct(DatabaseObject_User $user,$db)
    {
        parent::__construct();
        $this->db = $db;
        $this->user = $user;
    }

    public function process(Zend_Controller_Request_Abstract $request)
    {
        //Supports JSON and Simple REST
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_fullName=property_exists($phpNative, "fullName")?$phpNative->fullName:null;
            $this->_firstName=property_exists($phpNative, "firstName")?$phpNative->firstName:null;
            $this->_lastName=property_exists($phpNative, "lastName")?$phpNative->lastName:null;
            $this->_usernameEmail=property_exists($phpNative, "usernameEmail")?$phpNative->usernameEmail:null;
            $this->_phone10=property_exists($phpNative, "phone10")?preg_replace("/[^0-9]/","",$phpNative->phone10):null;
        }else{
            $this->addError('api','Only available for webform');
        }

        //VALIDATIONS
        if ($this->_fullName){
            if (strlen($this->_fullName) == 0 )$this->addError('fullName','Please enter your full name');
        }

        if ($this->_firstName && $this->_lastName ){
            if (strlen($this->_firstName) == 0 )$this->addError('firstName','Please enter your firstname');
            if (strlen($this->_lastName) == 0 )$this->addError('lastName','Please enter your lastname');
        }

        if (strlen($this->_usernameEmail) == 0 )$this->addError('usernameEmail','Please enter a valid usernameEmail');

        if (strlen($this->_phone10)!=10){
            $this->addError('phone10','Please enter a valid phone number');
        }

        if (!$this->hasError()) {
           $profileEnum=$this->user->profileEnum;

            if($this->_fullName){
                $fullName=explode(' ',$this->_fullName);
                if($fullName[0])$this->user->profile->$profileEnum['firstName']=$fullName[0];
                if(count($fullName)<2){
                    $this->user->profile->$profileEnum['lastName']="";
                }
                if(count($fullName)==2){
                    if($fullName[1])$this->user->profile->$profileEnum['lastName']=$fullName[1];
                }
                if(count($fullName)>2){
                   $lastnameInd=count($fullName)-1;
                   $this->user->profile->$profileEnum['lastName']=$fullName[$lastnameInd];
               }
           }
           if($this->_firstName)$this->user->profile->$profileEnum['firstName']=$this->_firstName;
           if($this->_lastName)$this->user->profile->$profileEnum['lastName']=$this->_lastName;
           if($this->_usernameEmail)$this->user->usernameEmail=$this->_usernameEmail;
           if($this->_phone10)$this->user->profile->$profileEnum['phone10']=$this->_phone10;

          $this->user->save();
        }
        return !$this->hasError();
    }



}
?>