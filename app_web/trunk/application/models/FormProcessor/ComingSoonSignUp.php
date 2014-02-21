<?
class FormProcessor_ComingSoonSignUp extends FormProcessor
{
    protected $db = null;
    public $comingsoon = null;
    protected $_validateOnly=false;

    public function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
        $this->comingsoon = new DatabaseObject_Person_ComingSoon($db);
    }

    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }

    public function process(Zend_Controller_Request_Abstract $request,$isPost=true)
    {
        if ($isPost){
            $this->email = $request->getPost('email');;
        }else{
            $this->email = $request->getParam('email');
        }

        $validator = new Zend_Validate_EmailAddress();
        if (strlen($this->usernameEmail) == 0)
            $this->addError('email', 'Please provide an email.');
        else if (!$validator->isValid($this->email))
            $this->addError('email', 'Invalid E-mail Address');
        else if ($this->comingsoon->usernameEmailExists($this->email)){
            $this->addError('email', 'This e-mail has been used before. We will kee you informed.');
        }
        else
            $this->comingsoon->email= $this->email;

        if (!$this->_validateOnly && !$this->hasError()) {
            //ad an ip to the profile before saving
            $this->comingsoon->recordAnalytics();

            $this->comingsoon->save();
        }

        // return true if no errors have occurred
        return !$this->hasError();
    }
}

?> 