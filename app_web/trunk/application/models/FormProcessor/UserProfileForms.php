<?
    class FormProcessor_UserProfileForms extends FormProcessor
    {
        protected $db = null;
        public $user = null;
		protected $userProfileFiledNameIDArr;
        public function __construct($db)
        {
            parent::__construct();
			$this->userProfileFiledNameIDAr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
			$auth = Zend_Auth::getInstance();
			$actIdent=$auth->getIdentity();
		
            $this->db = $db;
            $this->user = new DatabaseObject_User($db);
			$this->user->load($actIdent->user_id);
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {  
		  $formType=$this->sanitize($request->getPost('formType'));
		   switch($formType){
		   			case 'subProfileForm':
						$this->processProfileForm($request);
						break;
						
		   			case 'subProfileImage':
						$this->processProfileForm();
						break;

		   			case 'subPersonalInfoForm':
						$this->processPersonalInfoForm($request);
						break;

		   			case 'subSecurityForm':
						$this->processSecurityForm($request);
						break;
						
		   			case 'subAdditionalInfo':
						$this->processSecurtityForm();
						break;												
						
					default:
						$this->addError('birthDate', 'Case Def');
						break;
		   }
		   	
		   
      
            if (!$this->hasError()) {
            	$this->user->profile->$userProfileFieldNameIDAr['ip'] = $_SERVER['REMOTE_ADDR'];
                $this->user->save(); //Assess how dangerours is this to change the name instead of just profile
            }

            // return true if no errors have occurred
            return !$this->hasError();
        }
		
		private function processProfileForm(Zend_Controller_Request_Abstract $request){
			$userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
			$alNumValidator= new Zend_Validate_Alnum();
			
			//First Name-Not required
			$this->firstName = $this->sanitize($request->getPost('firstName'));
			if (strlen($this->firstName)>0){
				if($alNumValidator->isValid($this->firstName)){
					$this->user->profile->$userProfileFieldNameIDArr['firstName']= $this->firstName;	
				}else{
					$this->addError('firstName', 'First Name must be made one word made up of letters or numbers');
				}
			}else{
				$this->addError('firstName', 'First Name is A Required Field');
			}
			
	
			//Middle Name-Not required
			$this->middleName = $this->sanitize($request->getPost('middleName'));
			//May need to check later to see if it is a valid name and not a curse word
			if (strlen($this->middleName)>0){
            	$this->user->profile->$userProfileFieldNameIDArr['middleName']= $this->middleName;
			}
			//Last Name-Not required
			$this->lastName = $this->sanitize($request->getPost('lastName'));
			//May need to check later to see if it is a valid name and not a curse word
			if (strlen($this->lastName)>0){
		 		if(!$alNumValidator->isValid($this->lastName)){
				   $this->addError('lastName', 'Last Name must be made up of letters or numbers');
				}
			}
			$this->user->profile->$userProfileFieldNameIDArr['lastName']= $this->lastName;			
			
			//Profile Name-Not required
			$this->profileName = $this->sanitize($request->getPost('profileName'));
			//First Check that they are trying to change their profile name
			if  (strcmp($this->user->profileName,$this->profileName)!=0){
				if (strlen($this->profileName)>0){
					$validity=DatabaseObject_User::isValidProfileName($this->profileName);
					if($validity['isValid']){
						$this->user->profileName= $this->profileName;			
					}else{
						$this->addError('profileName',$validity['reason']);
					}
				}else{
					$this->user->profileName='';
				}
			}
		
			//Gender
			$this->gender = $this->sanitize($request->getPost('gender'));
			 if (strlen($this->gender) == 0){
               //Double Check to make certain it is one of the valid responses
			 }
      		 else
                $this->user->profile->$userProfileFieldNameIDArr['gender']= $this->gender;	
	
			//About Me-Not required
			$this->aboutMe = $this->sanitize($request->getPost('aboutMe'));
			$this->user->profile->$userProfileFieldNameIDArr['aboutMe']= $this->aboutMe;
				
			//Personal URL Page- Check the validity of it - add no follow
			$this->personalURL = $this->sanitize($request->getPost('personalURL'));
			 if (strlen($this->personalURL) == 0){
               // $this->addError('personalURL', 'Please enter a personalURL');
			 }
      		 else
                $this->user->profile->$userProfileFieldNameIDArr['personalURL']= $this->personalURL;
			
		}
		
		private function processPersonalInfoForm(Zend_Controller_Request_Abstract $request){			
			$subFormType=$this->sanitize($request->getPost('subFormType'));
			
			
		   switch($subFormType){
		   			case 'identification':
						$this->processPersonalInfoForm_Identification($request);
						break;
						
					default:
						$this->addError('birthDate', 'Case Def');
						break;
		   }
		   	
			
		}
			
		private function processPersonalInfoForm_Identification(Zend_Controller_Request_Abstract $request){
			$userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
			//birthDate
			//$this->addError('birthDate', 'IN  personal info');
			//add a checkbox for selecting/enablin
			$this->birthDateMonth = $this->sanitize($request->getPost('birthDateMonth'));
			$this->birthDateDay = $this->sanitize($request->getPost('birthDateDay'));
			$this->birthDateYear = $this->sanitize($request->getPost('birthDateYear'));
			 
			//ISO8601
			$this->user->profile->$userProfileFieldNameIDArr['birthDate']=$this->birthDateYear.'-'.$this->birthDateMonth.'-'.$this->birthDateDay;  
			
			//hPhone10
			$this->hPhone10 = $this->sanitize($request->getPost('hPhone10'));
			 if (strlen($this->hPhone10)> 0){
                $this->user->profile->$userProfileFieldNameIDArr['hPhone10']= $this->hPhone10;
			 }
			//hPhone10on
			$this->hPhone10on = $this->sanitize($request->getPost('hPhone10on'));
			 if (strlen($this->hPhone10on) > 0){
                $this->user->profile->$userProfileFieldNameIDArr['hPhone10on']= $this->hPhone10on;	
			 }

		}
			
		
		
		private function processSecurityForm(Zend_Controller_Request_Abstract $request){			
			$subFormType=$this->sanitize($request->getPost('subFormType'));
			
		   switch($subFormType){
		   			case 'changePassword':
						$this->processSecurityForm_ChangePassword($request);
						break;
				
					default:
						$this->addError('newPassword', 'Case Def');
						break;
		   }
		   	
			
		}
		
		private function processSecurityForm_ChangePassword(Zend_Controller_Request_Abstract $request){
			$this->oldPassword = $this->sanitize($request->getPost('oldPassword'));
			//Test against old password

			if (strcmp($this->user->password,md5($this->oldPassword))!=0){ //Move the function into a function
                $this->addError('oldPassword', 'Old Password is invalid.'); //Count entries  
			 }
			 else {//change password
				$this->newPassword = $this->sanitize($request->getPost('newPassword'));
				$this->confirmPassword = $this->sanitize($request->getPost('confirmPassword'));
				if (strlen($this->newPassword) == 0){
                	$this->addError('newPassword', 'Must not be blank.');   
				}
				
				if (strlen($this->confirmPassword) == 0){
                	$this->addError('confirmPassword', 'Must not be blank');   
				}
				
				if((strcmp($this->newPassword,$this->confirmPassword)==0)&& !$this->hasError()){
					//Change password
					$this->user->password=md5($this->newPassword);
				}else
				$this->addError('confirmPassword', 'New Password does not match confirmation');
			 }
			 if (strlen($this->user->password)==0){ //Move the function into a function
                $this->addError('oldPassword', 'Field cannot be empty.'); //Count entries  
			 }
		}
		
		
    }
?>