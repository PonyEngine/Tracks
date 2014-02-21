<?php
class SettingsController extends CustomControllerAction {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { //user logged in
            $actIdent=$auth->getIdentity();
            $this->_forward('/profile');
        }
    }
    public function profileAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $this->view->menuSelect="dashboard_settings";
        $this->view->user=$theUser;

        $profileEnum=$theUser->profileEnum;
        $this->view->fullName=$theUser->profile->$profileEnum['firstName'].' '.$theUser->profile->$profileEnum['lastName'];
        $this->view->usernameEmail=$theUser->usernameEmail;
        $this->view->phone10=SavCo_FunctionsGen::FormatPhone($theUser->profile->$profileEnum['phone10']);
        $this->jsCode[]=$this->config->url->js."_jquery/jquery.form.js";
        //$this->cssCode[]=$this->config->url->css."/imageareaselect/imgareaselect-default.css";
        $this->view->settingsActive='profile';
    }

    public function passwordAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $this->view->menuSelect="dashboard_settings";
        $this->view->user=$theUser;
        $this->jsCode[]=$this->config->url->js."_jquery/jquery.form.js";
        //$this->cssCode[]=$this->config->url->css."/imageareaselect/imgareaselect-default.css";
        $this->view->settingsActive='password';
    }
    public function updateprofileAction() {
        $data=array();
        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }
        $user= new DatabaseObject_User($this->db);
        $user->load($this->identity->user_id);
        $fp = new FormProcessor_UserProfile($user,$this->db);

        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']="Your profile has been updated";
        }else{
            $status['code']='300';
            $status['msg']="Not able to update your profile";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;
        if(count($data))$response['data']=$data;

        $this->sendJsonText($response);
    }
    public function changepasswordAction() {
        $data=array();
        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }
        $user= new DatabaseObject_User($this->db);
        $user->load($this->identity->user_id);

        $fp = new FormProcessor_UserChangePassword($this->db,$user);
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']="Your Password has been Changed";
        }else{
            $status['code']='300';
            $status['msg']="Sorry, not able to change your password";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;
        if(count($data))$response['data']=$data;

        $this->sendJsonText($response);
    }
}
?>
