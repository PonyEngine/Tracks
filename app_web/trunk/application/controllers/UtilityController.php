<?php
    class UtilityController extends CustomControllerAction
    {
        public function captchaAction()
        {
            $session = new Zend_Session_Namespace('captcha');

            // check for existing phrase in session
            $phrase = null;
            if (isset($session->phrase) && strlen($session->phrase) > 0)
                $phrase = $session->phrase;

            $captcha = Text_CAPTCHA::factory('Image');

            $opts = array('font_size' => 20,
                          'font_path' => Zend_Registry::get('config')->paths->data,
                          'font_file' => 'VeraBd.ttf');

            $captcha->init(120, 60, $phrase, $opts);

            // write the phrase to session
            $session->phrase = $captcha->getPhrase();

            // disable auto-rendering since we're outputting an image
            $this->_helper->viewRenderer->setNoRender();

            header('Content-type: image/png');
            echo $captcha->getCAPTCHAAsPng();
        }

   

  		 public function imageAction()
        {
            $request  = $this->getRequest();
            $response = $this->getResponse();

		  	$type = $request->getQuery('type');
            $id = (int) $request->getQuery('id');
            $w  = (int) $request->getQuery('w');
            $h  = (int) $request->getQuery('h');
            $hash = $request->getQuery('hash');

            $realHash = DatabaseObject_UserImage::GetImageHash($id, $w, $h,$type);

            // disable autorendering since we're outputting an image
            $this->_helper->viewRenderer->setNoRender();

            $image = new DatabaseObject_UserImage($this->db);
			
			if ($hash != $realHash || !$image->load($id)) {
                // image not found- return no Image instead of nothing
                $response->setHttpResponseCode(404);
                return;
            }

            try {
                $fullpath = $image->createThumbnail($w, $h,$type);
            }
            catch (Exception $ex) {
                $fullpath = $image->getFullPath();
            }

            $info = getImageSize($fullpath);

            $response->setHeader('content-type', $info['mime']);
            $response->setHeader('content-length', filesize($fullpath));
            echo file_get_contents($fullpath);
        }
    
    
		
	 public function imageformAction()
     {
            $request = $this->getRequest();
            $id = (int)$request->getPost('id');

			$redirect=$request->getPost('redirect');
			switch($request->getParam('act')){
				case 'profileImage':
					$user = new DatabaseObject_User($this->db);
					//Check that this is the user accessing her AccountController
					if ($this->identity->user_id!=$id ){
						$this->_redirect($this->getUrl());
					}
					else{
						$user->load($id);
					}
				break;
				
				
				default:
					$this->_redirect($this->getUrl());
					
			}

            $json = array();
            if ($request->getPost('upload')) {
                $fp = new FormProcessor_UserImage($user);
                if ($fp->process($request))
                    //$this->messenger->addMessage('Image uploaded');
					echo "Image Uploaded";
                else {
                    foreach ($fp->getErrors() as $error){
                    	echo $error;
                    // $this->messenger->addMessage($error);
					}
                       
                }
            }
            else if ($request->getPost('reorder')) {
                $order = $request->getPost('user_images');
                $user->setImageOrder($order);
            }
            else if ($request->getPost('delete')) {
                $image_id = (int) $request->getPost('image');
                $image = new DatabasObject_UserImage($this->db);
                if ($image->loadForPost($user->getId(), $image_id)) {
                    $image->delete();
                    if ($request->isXmlHttpRequest()) {
                        $json = array(
                            'deleted'  => true,
                            'image_id' => $image_id
                        );
                    }
                    //else
                      //  $this->messenger->addMessage('Image deleted');
                }
            }

            if ($request->isXmlHttpRequest()) {
                $this->sendJson($json);
            }
            else {
                $this->_redirect($redirect);
            }
        }

        public function fileuploadAction() {
            if(!$this->identity->user_id){   //Could be moved to a function
                $this->_redirect('/');
            }
            $request = $this->getRequest();
            /* if(!$request->isXmlHttpRequest()){
                   $this->_redirect('/');
            }*/
            $thisUser= new DatabaseObject_User($this->db);
            $thisUser->load($this->identity->user_id);


            $filePostName=$request->getPost('postname');
            $fileType=$request->getPost('fileType');

            $fp=null;
            $data=array();
            switch($fileType){
                case "image_user":
                    $fp=new FormProcessor_File_Image_User($thisUser,$filePostName,false);
                    break;
                case "image_brandimgasset": //Images for Branded Event's Pages/App/Email
                    $emptyBrand= new DatabaseObject_Brand($this->db);
                    $fp=new FormProcessor_File_Image_Brandimgasset($emptyBrand,$filePostName,false);
                break;
                default:
                    $fp=new FormProcessor_File($thisUser,$filePostName);
                break;
            }

            try {
                if($fp->process($request)){
                    $status['code']='200';
                    $status['msg']=$fp->uploadMsg();
                    $fileInfoOfUploaded=$fp->fileInfoOfUploaded();
                    $data['fileInfo']['fileName']=$fileInfoOfUploaded['fileName'];
                    $data['fileInfo']['filePath']=$fileInfoOfUploaded['filePath'];
                }else{
                    $status['code']='300';
                    $status['msg']="Could not upload the image";
                    $status['errors']=$fp->getErrors();
                }
            } catch (MyException $e) {
                $status['code']='300';
                $status['msg']=$e->getMessage();
            }

            $response['status']=$status;
            if(count($data)>0)$response['data']=$data;

            $this->sendJsonText($response);

        }
    }
?>