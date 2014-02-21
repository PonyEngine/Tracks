<?
class ApiController extends CustomControllerAction{
    private $responseType="json";   //Version this later

    public function indexAction(){
        $this->_redirect('/');
    }

    public function authenticateAction(){
        //api/authenticate?usernameEmail=profileName&password=demo&device_id=1&version_id=1.1.3&lat=34.017&lon=-118.495
        $request = $this->getRequest();
        $userLogin= new FormProcessor_UserLogin($this->db);
        $userLogin->apiLogin(TRUE);
        $status=array();
        $data=array();
        if ($userLogin->process($request)){
            $status['code']='200';
            $status['msg']="User Is Authenticated";
            $authUser=$userLogin->user;
            $data['userId']=$authUser->getId();
            $data['userIsAuthenticated']=true;
            $data['role']=$authUser->user_type;
            $data['userAuthToken']=$authUser->session->sessionId;
            $data['tsLastLogin']=$authUser->tsLastLogin;
            $data['version']=$this->config->version->num;
        }else{
            $status['code']='401';
            $status['errors']=implode(",",$userLogin->getErrors());
            $data['userId']=0;
            $data['userIsAuthenticated']=false;
            $data['role']=null;
            $data['userAuthToken']="";
            $data['version']='0.0';
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    public function registerAction(){
        //api/register?fullname=John Doe&email=enter real address for email.com&password=passwd&username=savco&phone=3105555545&userPhoto=file,/dirname/filename/&lat=34.017&lon=-118.495
        $request = $this->getRequest();
        $status=array();
        $data=array();

        $fp= new FormProcessor_UserRegister($this->db);
        $fp->apiRegister(TRUE);
        if ($fp->process($request)){
            //Status
            $status['code']='200';
            $status['msg']="User Has Been Signed Up and Authenticated";
            $authUser=$fp->user;
            $data['userId']=$authUser->getId();
            $data['userIsAuthenticated']=true;
            $data['userAuthToken']=$authUser->session->sessionId;
        }else{
            $status['code']='401';
            $status['params']=$request->getParams();
            $status['errors']=implode(",",$fp->getErrors());
            $data['userID']=0;
            $data['userIsAuthenticated']=false;
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    public function fbconnectAction(){
        //api/fbconnect?fbId=fbId&fbName=Name from Facebook&fbFName=firstName&fbMName=MiddleName&fbLName=LastName&fbEmail=EmailAddress&fbGender=&fbProfileURL=&fbPermissions=&fbUsername=&fbLocale=iOS Location;&device_id=1&device_version=5.1.3&device_token=sbkafgagfyatewyfgtywe78&version=1.1.3&lat=34.017&lon=-118.495
        $request = $this->getRequest();
        $status=array();
        $data=array();

        if ($request->getParam('debug')){
            $data['params']=$request->getParams();
            $data['FBNAME']=$request->getParam('fbName');
            $data['USERNAME']=$request->getParam('fbUsername');
        }else{
            $fbId=$request->getParam('fbId');
            $userLoginRegister= new FormProcessor_UserFBLoginRegister($this->db);
            $userLoginRegister->apiLogin(TRUE);
            if ($userLoginRegister->process($request)){
                $status['code']='200';
                $status['msg']="User Has Been Signed Up and Authenticated";
                $authUser=$userLoginRegister->user;
                $data['userId']=$authUser->getId();
                $data['userIsAuthenticated']=true;
                $data['userAuthToken']=$authUser->session->sessionId;
                $data['role']=$authUser->user_type;
                $data['user']=$authUser->arrayRepresentation($this->config);
                $data['authType']=$userLoginRegister->authType();
                $data['tsLastLogin']=$authUser->tsLastLogin;
            }else{
                $status['code']='400';
                $status['msg']="Could not connect user and Authenticate";
                $data['userId']=0;
                $data['userIsAuthenticated']=false;
                $data['userAuthToken']="";
            }
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    //**************************************************************************************
    //**************************************************************************************
    //****   User Authenticated Actions
    //**************************************************************************************
    //**************************************************************************************
     //User- Read, Update,Delete
    //Returns JSON Objects: User
    public function userprofilewithcredentialsAction() {
        //api/userprofilewithcredentials?userId=text,1&authToken=text, user authentication token&lat=34.017&lon=-118.495
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'),$request->getParam('lat'),$request->getParam('lon'))){  //Check Session
            $status['code']='200';
            $data['profile']=$user->arrayRepresentationProfile($this->config);
            $data['profile']['bets']=$user->betsCount();
            $userBets=DatabaseObject_UserBet::BetCountBySportsGroup($this->db);
            $data['profile']['betTotalNFL']=$userBets['nfl']?(int)$userBets['nfl']:0;
            $data['profile']['betTotalNBA']=$userBets['nba']?(int)$userBets['nba']:0;
            $data['profile']['betTotalMLB']=$userBets['mlb']?(int)$userBets['mlb']:0;
            $data['profile']['betTotalNHL']=$userBets['nhl']?(int)$userBets['nhl']:0;
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    public function usersocialwithcredentialsAction() {
        //api/usersocialwithcredentials?userId=text,1&authToken=text, user authentication token&lat=34.017&lon=-118.495
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'))){  //Check Session
            //Check Session
            $status['code']='200';
            $data['social']=$user->arrayRepresentationSocial();
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    public function useropeninviteswithcredsAction() {
        //api/useropeninviteswithcreds?userId=text,1&authToken=text, user authentication token&lat=34.017&lon=-118.495
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'))){  //Check Session
            //Check Session
            $status['code']='200';
            $data['invites']=$user->arrayRepresentationOpenInvites();
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    public function userwithidAction() { //Deprecated
        //api/userwithid?userId=text,1&authToken=text, user authentication token&requestUserId=2&lat=34.017&lon=-118.495
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'),$request->getParam('lat'),$request->getParam('lon'))){
            $requestedUser= new DatabaseObject_User($this->db);
            if($requestedUser->load((int)$request->getParam('requestUserId'))){
                //Check Session
                $status['code']='200';
                $data['requestUser']=$requestedUser->arrayRepresentationSimpleProfile($this->config);
            }else{
                $status['code']='402';
                $status['msg']="Did not find user";
            }
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }


    //Collections
    public function userwithcredentialsAction() {
        //api/userwithcredentials?userId=text,1&authToken=text, user authentication token&lat=34.017&lon=-118.495
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'))){  //Check Session
            //Check Session
            $status['code']='200';
            $data['user']=$user->arrayRepresentation($this->config);
            //  $status['params']=$request->getParams();
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }


    public function addinvitesAction() {
        //api/addinvites?userId=text,1&authToken=text,user authentication token&userBetId=2&friendIdsNames=11111111111,Sam Johnson;222222222,Mark Lu;3333333333333,Mary Johnson&lat=34.017&lon=-118.495
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'))){  //Check Session
            $fp = new FormProcessor_AddFriendInvites($user);
            $status=array();
            if ($fp->process($request)){
                $status['code']='200';
                $data['msg']=$fp->msg();
            }else{
                $status['code']='300';
                $status['msg']="Could not add invites";
                $status['errors']=implode(",",$fp->getErrors());
            }
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }


    public function inviteresponseAction() {
        //api/inviteresponse?userId=text,1&authToken=text, user authentication token&userBetInviteId=15&isAccepting=false&teamId=null,23&lat=34.017&lon=-118.495
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        $data=array();
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'),$request->getParam('lat'),$request->getParam('lon'))){
            $fp = new FormProcessor_RespondToInvite($user,$this->config);
            $status=array();
            if ($fp->process($request)){
                $status['code']='200';
                $status['msg']=$fp->msg();
                $data['og_url']=$fp->ogURL();
            }else{
                $status['code']='300';
                $status['msg']="Could not respond to the invite";
                $status['errors']=implode(",",$fp->getErrors());
            }
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    //Collections
    public function userleaderswithfilterAction() {
        //api/userleaderswithfilter?userId=int,1&authToken=text, user authentication token&filterType=points&lat=34.017&lon=-118.495
        //points,bucks,friendpoints,friendbucks
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'))){  //Check Session
            $fp = new FormProcessor_UserLeaders($user,$this->config);
            if ($fp->process($request)){
                $status['code']='200';
                $data['users']=$fp->leaders();
            }else{
                $status['code']='300';
                $status['msg']="Could not respond to the invite";
                $status['errors']=implode(",",$fp->getErrors());
            }
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    //Collections
    public function ponypushiosAction() {
        //api/ponypushios?userId=int,1&authToken=text, user authentication token&sendToUserId=3&sendMsg=How do you do&sendBadgeCount=3&sendSoundURL=http://www.test.com&lat=34.017&lon=-118.495
        //points,bucks,friendpoints,friendbucks
        $status=array();
        $data=array();
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'))){  //Check Session
            $fp = new FormProcessor_SendPushIOS($user,$this->config);
            if ($fp->process($request)){
                $status['code']='200';
                $data['msg']="Push Notification Sent";
            }else{
                $status['code']='300';
                $status['errors']=implode(",",$fp->getErrors());
            }
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $response['data']=$data;
        $this->sendJson($response);
    }

    public function runapiAction() {
        //http://tracks.ponyengine.com/api/runapi?userId=1&authToken={A7432E08-DA65-22E6-0257-73E4A86A70E5}&lat=34.017&lon=-118.495
        //api/runapi?userId=text,1&authToken=text, user authentication token&lat=34.017&lon=-118.495
        $request = $this->getRequest();
        $user= new DatabaseObject_User($this->db);
        if ($user->loadByValidatedSession($request->getParam('userId'), $request->getParam('authToken'),$request->getParam('lat'),$request->getParam('lon'))){
            $fp = new FormProcessor_APISports($user);
            $status=array();
            if ($fp->process($request)){
                $status['code']='200';
                $status['msg']=$fp->msg();
            }else{
                $status['code']='300';
                $status['msg']="Could not respond to the invite";
                $status['errors']=implode(",",$fp->getErrors());
            }
        }else{
            $status['code']='401';
            $status['msg']="Could not authenticate";
        }
        $response['status']=$status;
        $this->sendJson($response);
    }


}