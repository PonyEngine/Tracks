<?php
    class DebugController extends CustomControllerAction
    {
         public function indexAction()
        {

		}

        public function facebookAction()
        {  $this->view->thebody=strip_tags($_REQUEST['body']);
            $pageURL = 'http://';
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }

        public function smsAction()
        {
            $smsAddress="";
            $dataArr=array();
            $dataArr['brandName']='Test Brand';
            $dataArr['eventName']='Test Event';
            $tpl="socialsharemail.tpl";
            $html=true;
            $fromName="no-reply";
            $emailFrom=Zend_Registry::get('config')->email->from->email;
            if(SavCo_ZendExtend::Email_FromName_From_To_Data_Template_isHTML($fromName,$emailFrom,$smsAddress,$dataArr,$tpl,$html)){
                //echo "Email has been sent";
            }
            $this->_helper->viewRenderer->setNoRender();
        }

        public function apicallsAction()
        {
            $restCalls=DatabaseObject_Apicall::GetApicallsWithIds(array());
            $this->view->restCalls=$restCalls;
            $this->jsCode[]=$this->config->url->js."_jquery/jquery.form.js";
        }
		
		public function formAction()
        {

        	$request = $this->getRequest();
			if($request->getPost()){
				$auth = Zend_Auth::getInstance();
				$actIdent=$auth->getIdentity();
				$user = new DatabaseObject_User($this->db);
		 		$user->load($actIdent->user_id);
        	
		    	$fp = new FormProcessor_File_Image_User($this->viewer,'img_src');
	   			$fp->process($request);
	   		}
        }

		public function testAction(){
			$branch= new DatabaseObject_Branch($this->db);
			$branch->load(1);
			echo "The name is ".$branch->branch_nameAddition;
			foreach($branch->locations as $aLocation){
	 			echo "<br />The lat is".$aLocation->branch_lat;
			}
			$this->_helper->viewRenderer->setNoRender();
		}

        public function testemailsetupAction(){//move to something like apicalls
            $to      = 'tracks@ponyengine.com';
            $subject = 'the subject';
            $message = 'hello';
            $headers = 'From: tracks@ponyengine.com' . "\r\n" .
                'Reply-To:  tracks@ponyengine.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            date_default_timezone_set('America/Chicago');

            $mail = mail($to, $subject, $message, $headers);
            if($mail){
                echo "YES";

            } else{
                echo "NO";
            }
            //noResponse
            $this->getHelper('ViewRenderer')->setNoRender();
        }

        public function getmd5Action(){ //MOve to something like apicalls
            $request = $this->getRequest();
            echo md5($request->getParam("password"));
        }

        public function sockettestAction(){

        $socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
        if (!$socket) {
            echo "$errstr ($errno)<br />\n";
        } else {
            while ($conn = stream_socket_accept($socket)) {
                fwrite($conn, 'The local time is ' . date('n/j/Y g:i a') . "\n");
                fclose($conn);
             }
            fclose($socket);
        }
        }

        public function playaudioAction(){
            $audioTrack= new DatabaseObject_File_Audio_Track($this->db,"test",1);
            $audioTrack->streammp3Audio();
        }

        public function audiopageAction(){
        }

        public  function sendemailtestAction(){
            $tpl="testemail.tpl";
            $dataArr=array();
            $html=false;

            $user= new DatabaseObject_User($this->db);
            if ($user->load($this->identity->user_id)){


                $email=sprintf("%s",$user->usernameEmail);

                try{
                    //Set values to be used in email
                    $templater= new Templater();
                    $templater->user=$user;
                    //$templater->tempArr=$this->tempArr;
                    $templater->config=Zend_Registry::get('config');
                    //$templater->dataArr=$dataArr;

                    //fetch the email body
                    $body= $templater->render('email/'.$tpl);

                    //extract the subject from the first line.
                    list($subject,$body)=preg_split('/\r|\n/',$body,2);

                    //now set-up and send the e-mail
                    $mail= new Zend_Mail();

                    //set the to address and the user's full name in the 'to' line
                    $mail->addTo($email);

                    //get the admin 'from details from the config
                    $mail->setFrom(Zend_Registry::get('config')->email->from->email,
                        Zend_Registry::get('config')->email->from->email);

                    //set the subject and body and send the mail
                    $mail->setSubject(trim($subject));
                    if(!$html){
                        $mail->setBodyText(trim($body));
                    }else{
                        $mail->setBodyHtml(trim($body));
                    }

                    $theMail=$mail->send();
                    $message=sprintf('Email Sent');
                    $logger= Zend_Registry::get('logEvent');
                    $logger->warn($theMail,1);
                    print_r($theMail);
                }
                catch(Exception $e){
                    $message=sprintf('Not able to email user');
                    $logger= Zend_Registry::get('logEvent');
                    $logger->warn($e->getMessage(),1);
                }
            }
            else{
                $message=sprintf('Id %d does not exist',$this->identity->user_id);
                $logger= Zend_Registry::get('logEvent');
                $logger->warn($message,1);
            }
            $this->getHelper('ViewRenderer')->setNoRender();
        }

        public function sendemailAction(){
            $user= new DatabaseObject_User($this->db);
            if ($user->load($this->identity->user_id)){
                $fromName="Test Team";
                $emailFrom=$this->config->email->from->email;
                $emailTo=$user->usernameEmail;
                $dataArr['user']=$user;
                $testEmailTpl="testemail.tpl";
                $isHtml=true;

                if(SavCo_ZendExtend::Email_FromName_From_To_Data_Template_isHTML("Test",$emailFrom,$emailTo,$dataArr,$testEmailTpl,$isHtml)){
                    echo "Email has been sent";
                }
            }else{
                echo "User could not be loaded";
            }
            $this->getHelper('ViewRenderer')->setNoRender();
        }

    }
	

	
	

?>