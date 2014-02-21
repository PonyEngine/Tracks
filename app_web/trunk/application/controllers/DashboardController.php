<?php
class DashboardController extends CustomControllerAction {
    public function indexAction() {
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }

        switch($this->identity->user_type){
            case "Admin":
            case "developer":
                $this->_forward('usermanagement');
            break;
            case "member": //ability to turn on/off based on if app has a web client
                $this->_forward('usermanagement');
                break;
            default:
                $this->_redirect('/');
            break;
        }
    }


    public function usermanagementAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        if($this->identity->user_type!="Admin" && $this->identity->user_type!="developer"){
            $this->_redirect('/');
        }
        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $friendIds=$theUser->fbAppUsersFriendIds($this->config);

        $memberLevels[]='developer';
        $memberLevels[]='Admin';
        $memberLevels[]='member';

        $this->view->memberLevels=$memberLevels;

        //CSS JSS LESS
        $this->jsCode[]=$this->config->url->js."_bootstrap/bootstrap-modal.js";
        $this->jsCode[]=$this->config->url->js."_tools/jquery.tablesorter.min.js";
        $this->cssCode[]=$this->config->url->js."_tools/blue/style.css";
        $this->jsCode[]=$this->config->url->js."_bootstrap/bootstrap-tab.js";
        $this->jsCode[]=$this->config->url->js."_bootstrap/bootstrap-datepicker.js";
        $this->cssCode[]=$this->config->url->css."_bootstrap/datepicker.css";
        $this->jsCode[]=$this->config->url->js."_jquery/jquery-ui-1.10.1.custom.min.js";
        $this->view->menuSelect="usermanagement";
    }

    public function logoutAction() {
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::expireSessionCookie();
        $this->facebook ->destroySession();
        $this->_redirect('/');
    }

    public function loginAction() {
        $theUser=null;
        $access_token=null;
        $userInfo=null;
        $uid=null;

        if(!$this->identity->user_id && $this->fbId){ //if it does not have authentication but there is an fbId
            if($theUser= DatabaseObject_User::GetUserWithFbId($this->fbId,$this->facebook)){
                //Login User
                $theUser->authenticateUser();
            }
        }else{
            echo "Could not authenticate";
        }
        $this->_redirect('/');
    }

    public function thanksAction() {
        $auth = Zend_Auth::getInstance();
        /*if ($auth->hasIdentity()) { //user logged in
                  $actIdent=$auth->getIdentity();
                  $this->_redirect('/account/home');
              }*/
        $user = new DatabaseObject_User($this->db);
        $user->load(1);
        $this->view->user=$user;
        $this->view->profileArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
        $this->view->menuSelect="dashboard";
    }

    public function saveprofileAction() {
        //Basic Checks for entering these calls
        if(!$this->identity->user_id){   //Could be moved to a function
            $this->_redirect('/');
        }
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }
        $status['code']='300';
        $status['msg']="Could not save profile";

        $newProfileJSON=$request->getPost('profile');
        $phpNativeProfile = Zend_Json::decode($newProfileJSON, Zend_Json::TYPE_OBJECT);

        //Operation
        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        //ProfileName Change
        $saveProfile=true;
        if (strcasecmp($theUser->profileName, $phpNativeProfile->profileName) !=0 && strlen($phpNativeProfile->profileName)>0){
            if (!$theUser->profileNameExists($phpNativeProfile->profileName)){
                $theUser->profileName=$phpNativeProfile->profileName;
            }else{
                $saveProfile=false;
                $status['msg']="Profile Name \"$phpNativeProfile->profileName\" is in use by another user.";
            }
        }
        //profileTmpImagPath
        if (strlen($phpNativeProfile->profileTmpImagPath)>0){
            $fp=new FormProcessor_File_Image_User($theUser,"");
            if ($fileNameInfo=$fp->processUploadedFile($phpNativeProfile->profileTmpImagPath,150,150,0,0)){
                $status['info']=$fileNameInfo;
            }else{
                $saveProfile=false;
                $status['msg']="Cannot upload image.";
            }
        }

        if($saveProfile){
            if($theUser->save()){
                $status['code']='200';
                $status['msg']="Profile Saved";
                $data['profileName']=$theUser->profileName;
            }else{
                $status['code']='300';
                $status['msg']="Could not save profile";
                $data['profileName']=$theUser->profileName;
            }
        }else{
            $status['code']='300';
            $status['msg']="Could not save with same Profile Name";
            $data['profileName']=$theUser->profileName;
        }



        $response['status']=$status;
        if(count($data))$response['data']=$data;

        $this->sendJson($response);
    }


    public function imageuploadAction() {
        if(!$this->identity->user_id){   //Could be moved to a function
            $this->_redirect('/');
        }
        $request = $this->getRequest();
        /*if(!$request->isXmlHttpRequest()){
                  $this->_redirect('/');
              }*/
        $thisUser= new DatabaseObject_User($this->db);
        $thisUser->load($this->identity->user_id);


        $filePostName=$request->getPost('postname');
        $fileType=(int)$request->getPost('fileType');

        //For some reason the comparison of strings caused some major issues. Investigate later
        //using strcasecmp
        $fp=null;
        // echo "FileTYpe is $fileType ";
        if($fileType==0){
            $fp=new FormProcessor_File_Image_User($thisUser,$filePostName);
        }elseif($fileType==1){
            //Create an empty campaign
            $emptyCampaign= new DatabaseObject_UserCampaign($this->db);
            $fp=new FormProcessor_File_Image_UserCampaign($emptyCampaign,$filePostName);
        }

        try {
            if($imageName=$fp->uploadToTmp($request)){ //Check that campaign is not a duplicate
                $status['code']='200';
                $status['msg']="Image uploded";
                $data['imageName']=$imageName;
            }else{
                $status['code']='300';
                $status['msg']="Could not publish Campaign";
            }
        } catch (MyException $e) {
            $status['code']='300';
            $status['msg']=$e->getMessage();
        }


        $response['status']=$status;
        if(count($data))$response['data']=$data;

        $this->sendJson($response);

    }

    public function createbrandandoruserAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        if($this->identity->user_type!="Admin" && $this->identity->user_type!="developer"){
            $this->_redirect('/');
        }

        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $fp = new FormProcessor_BrandUserCreate($this->db,$theUser);
        $status=array();
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']=$fp->getCreated();
        }else{
            $status['code']='300';
            $status['msg']="Could not create user";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;

        $this->sendJsonText($response);
    }

    public function requestendeventAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $fp = new FormProcessor_EventEnd($this->db,$theUser);
        $status=array();
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']=$fp->endingMsg();
        }else{
            $status['code']='300';
            $status['msg']="Could not end the event";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;

        $this->sendJsonText($response);
    }

    public function createbrandeventAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $fp = new FormProcessor_BrandEventCreate($this->db,$theUser);
        $status=array();
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']=$fp->getCreated();
        }else{
            $status['code']='300';
            $status['msg']="Could not create or update";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;

        $this->sendJsonText($response);
    }

    public function sendusagereportAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }

        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $fp = new FormProcessor_SendUsageReport($this->db,$theUser);
        $status=array();
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']="Email Sent";
        }else{
            $status['code']='300';
            $status['msg']="Could not send usage report";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;

        $this->sendJsonText($response);
    }

    public function searchusersAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        if($this->identity->user_type!="Admin" && $this->identity->user_type!="developer"){
            $this->_redirect('/');
        }

        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $fp = new FormProcessor_UserSearch($this->db,$theUser,$this->config);
        $status=array();
        if ($fp->process($request)){
            $status['code']='200';
            $data['foundusers']=$fp->foundUsers();
            $data['founduserCount']=count($fp->foundUsers());
        }else{
            $status['code']='300';
            $status['msg']="Could not create user";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;
        $response['data']=$data;

        $this->sendJson($response);
    }

    public function updateuserAction() {
        $theUser=null;
        if(!$this->auth->hasIdentity()){
            $this->_redirect('/');
        }
        if($this->identity->user_type!="Admin" && $this->identity->user_type!="developer"){
            $this->_redirect('/');
        }

        //Basic Checks for entering these calls
        $request = $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->_redirect('/');
        }

        $theUser= new DatabaseObject_User($this->db);
        $theUser->load($this->identity->user_id);

        $fp = new FormProcessor_UserUpdate($this->db);
        $status=array();
        $data=array();
        if ($fp->process($request)){
            $status['code']='200';
            $status['msg']=$fp->created();
        }else{
            $status['code']='300';
            $status['msg']="Could not create user";
            $status['errors']=$fp->getErrors();
        }

        $response['status']=$status;
        $response['data']=$data;

        $this->sendJson($response);
    }
}