<?php

 class ImageProcessor 
    {	    	public function Process($request){
    		$config=Zend_Registry::get('config');
			if($request->getParam('act') == 'save'){
	
				//move to user area- so there can be users,etc
				$auth = Zend_Auth::getInstance();
				$actIdent=$auth->getIdentity();
		
				$user = new DatabaseObject_User($this->db);
		 		$user->load($actIdent->user_id);
				
				$fp= new FormProcessor_File_Image_User($user,'img_src');
				$fp->process($request);	
			}
			elseif($request->getParam('act') == 'upload'){
				$big_arr = array(
				'uploaddir'	=> $config->atemp->imagebig,
				'tempdir'	=> $config->atemp->image,
				'height'	=> $request->getPost('height'),
				'width'		=> $request->getPost('width'),
				'x'			=> 0,
				'y'			=> 0,				'thumb'     => false);
				ImageHandler::ResizeImg($big_arr);
			}
			else
			{
				echo "No Image";
			}
    		exit;
    	}
    }
?>