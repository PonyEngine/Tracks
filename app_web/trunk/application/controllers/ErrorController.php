<?php
    class ErrorController extends CustomControllerAction
    {
        public function errorAction()
        {
            $request = $this->getRequest();
            $error = $request->getParam('error_handler');
			$header=array('title'=>'Error');
			$this->breadcrumbs->addStep($header);
			
            switch ($error->type) {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    $this->_forward('error404');
                    return;

                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                default:
                    // fall through
            }

            $this->getResponse()->clearBody();

           $logger= Zend_Registry::get('logEvent')->crit($error->exception->getMessage());
        }

        public function error404Action()
        {
        	$auth = Zend_Auth::getInstance();
        	if ($auth->hasIdentity()) {// if a user's already logged in, send them to their account home page
            	$this->viewer = new DatabaseObject_User($this->db);
            	$this->actIdent=$auth->getIdentity();
		 		$this->viewer->load($this->actIdent->user_id);
		 		$this->view->viewer=$this->viewer;
        	}
        	
        	
        	
            $request = $this->getRequest();
            $error   = $request->getParam('error_handler');
            $uri     = $request->getRequestUri();

            $logger= Zend_Registry::get('logEvent')->info('404 error occurred: ' . $uri);

            $this->getResponse()->setHttpResponseCode(404);

			$header=array('title'=>'404 ERROR: Page Not Found');
			$this->breadcrumbs->addStep($header);
            $this->view->requestedAddress = $uri;
        }
    }
?>