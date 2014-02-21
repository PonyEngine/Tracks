<?
	class IndexController extends CustomControllerAction{
	 	
		public function indexAction(){
			$auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity() ){
                 switch($this->identity->user_type){
                    case "Admin":
                    case "developer":
                    case "member":     //Have switch to turn this on and off
                        $this->_forward('profile');
                    break;
                    default:
                        $this->_forward('thankyou');
                    break;
		        }
            }
        }

        public function thankyouAction(){
            $auth = Zend_Auth::getInstance();
            if (!$auth->hasIdentity() ){
                $this->_redirect("/");
            }
        }

        public function profileAction(){
            $auth = Zend_Auth::getInstance();
            if (!$auth->hasIdentity() ){
                $this->_redirect("/");
            }
            $user= new DatabaseObject_User($this->db);
            $user->load($this->identity->user_id);
            $this->view->user=$user;
        }
	}
