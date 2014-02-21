<?
    class FormProcessor_LaunchEmailRegistration extends FormProcessor
    {
        protected $db = null;
        public $launchemail = null;

        public function __construct($db)
        {
            parent::__construct();
            $this->db = $db;
            $this->launchemail = new DatabaseObject_Launchemail($db);
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
           
            // validate the e-mail address
            $this->email = $this->sanitize($request->getPost('email'));
            $validator = new Zend_Validate_EmailAddress();
			
            if (strlen($this->email) == 0)
                $this->addError('email', 'Please enter your e-mail address');
            else if (!$validator->isValid($this->email))
                $this->addError('email', 'Please enter a valid e-mail address');
            else
                $this->launchemail->email= $this->email;

           

            // if no errors have occurred, save the user
            if (!$this->hasError()) {
                $this->launchemail->save();
            }

            // return true if no errors have occurred
            return !$this->hasError();
        }
    }
?>