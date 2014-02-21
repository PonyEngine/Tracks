<?php
class AccountController extends CustomControllerAction {

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { //user logged in
            $actIdent=$auth->getIdentity();
            $this->_redirect('/');
        }
    }
    public function loginAction(){
        if ($this->auth->hasIdentity()) { //user logged in
            $this->_redirect('/');
        }
        $recaptcha = new Zend_Service_ReCaptcha($this->config->recaptcha->pubkey, $this->config->recaptcha->privkey);
        $this->view->recaptcha=$recaptcha->getHTML();
        //Mail Hide
        $this->jsCode[]=$this->config->url->js."_jquery/jquery.form.js";
    }

    public function registerAction(){
        if ($this->auth->hasIdentity()) { //user logged in
            $this->_redirect('/');
        }
        $recaptcha = new Zend_Service_ReCaptcha($this->config->recaptcha->pubkey, $this->config->recaptcha->privkey);
        $this->view->recaptcha=$recaptcha->getHTML();
        //Mail Hide
        /*$mail="me@tracks.ponyengine.com";
        $mailHide = new Zend_Service_ReCaptcha_MailHide();
        $mailHide->setPublicKey($this->config->recaptcha->pubkey);
        $mailHide->setPrivateKey($this->config->recaptcha->privkey);
        $this->view->mailHide=$mailHide->setEmail($mail);*/
        $this->jsCode[]=$this->config->url->js."_jquery/jquery.form.js";
    }


    public function fbloginAction() {
        $theUser=null;
        $access_token=null;
        $userInfo=null;
        $uid=null;

        if(!$this->identity->user_id && $this->fbId!=null){ //if it does not have authentication but there is an fbId
            if($theUser= DatabaseObject_User::GetUserWithFbId($this->fbId,$this->facebook)){
                //Login User
                $theUser->authenticateUser();
            }
        }else{
            //echo "Could not authenticate";
        }
        $this->_redirect('/');
    }

    public function fblogoutAction() {
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::expireSessionCookie(); //Is this working
        $this->facebook ->destroySession();
        $this->_redirect('/');
    }


    public function logoutAction() {
        $this->viewer= new DatabaseObject_User($this->db);
        $this->viewer->load($this->identity->user_id);
        $this->view->viewer=$this->viewer;

        $request=$this->getRequest();

        $tagCookies=$request->getCookie('selTags');

        $cookieSelectedTagsArr=explode(';',$tagCookies);

        if (count($cookieSelectedTagsArr)>0){ //if set

            $this->viewer->updateTagsWithIds($cookieSelectedTagsArr);

        }

        //Save selectionTags if diff
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::expireSessionCookie(); //Is this working

        //Cookie Deletion
        setcookie ('selTags','');
        $this->_redirect('/');
    }


    public function requestloginAction() {
        $data=array();
        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        //if($this->auth->hasIdentity()){   //Could be moved to a function
        //    $this->_redirect('/');
        //}

        $fp = new FormProcessor_UserLogin($this->db);
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']="All Good Reload";
        }else{
            $status['code']='300';
            $status['msg']="Not able to log you in";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;
        if(count($data))$response['data']=$data;

        $this->sendJson($response);
    }

    public function requestregisterAction() {
        $data=array();
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $fp = new FormProcessor_UserRegister($this->db);
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']="All Good Reload";
        }else{
            $status['code']='300';
            $status['msg']="Not able to sign you up";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;
        if(count($data))$response['data']=$data;

        $this->sendJson($response);
    }


    public function requestlogoutAction() {
        $this->auth->clearIdentity();
        Zend_Session::expireSessionCookie();
        //Destroy other Sessions
        try{
            if($this->fbId){
                $this->facebook->destroySession();
            }
        }catch(Exception $e){
            $this->_redirect('/');
        }
        $this->_redirect('/');
    }


}
?>
