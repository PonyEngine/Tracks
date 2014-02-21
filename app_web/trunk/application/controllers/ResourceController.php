<?php
    class ResourceController extends CustomControllerAction
    {
  		 public function imageAction()
        {
            $request  = $this->getRequest();
            $response = $this->getResponse();
            $this->_helper->viewRenderer->setNoRender(); //Disable Auto Rendering
		  	$type = $request->getQuery('type');  //use as directory
            $id = (int) $request->getQuery('id');
            $w  = (int) $request->getQuery('w');
            $h  = (int) $request->getQuery('h');
            $hash = $request->getQuery('hash');
            $fileHash = DatabaseObject_File_Image::GetFileHash($id, $w, $h,$type);

			/*if ($hash != $fileHash) {
				echo $hash.'<br />';
				echo "FILE HASH ---- $fileHash";
                // image not found- return no Image instead of nothing
                $response->setHttpResponseCode(404);
                return;
			 }*/ 
         
			$fullpath=DatabaseObject_File_Image::GetThumbnailPathWithDirFileHash($type,$hash);
			//echo "the full path is $fullpath";
			if (file_exists($fullpath)){
   	        	//Outputting the Image
    	       	$info = getImageSize($fullpath);
				$response->setHeader('Content-Type', $info['mime']);
            	$response->setHeader('Content-Length', filesize($fullpath));
              	// Set the content type
   				echo file_get_contents($fullpath);
			}
   }
    	public function barcodegenAction(){	
			Zend_Barcode::render('code39','image',
			array('text'=>'681477','font'=>3));

			Zend_Barcode::render('code39','image',
			array('text'=>'554938','font'=>3));

			Zend_Barcode::render('code39','image',
			array('text'=>'672407','font'=>3));
			$this->_helper->viewRenderer->setNoRender();
		}
    }