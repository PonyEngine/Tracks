<?php
    function smarty_function_imagefilename($params, $smarty)
    {
        //Works with all image objects
    	$fileObject=isset($params['fileObject'])?$params['fileObject']:0;
		$width=isset($params['w'])?$params['w']:0;
		$height=isset($params['h'])?$params['h']:0;

        require_once $smarty->_get_plugin_filepath('function', 'geturl');

        $config=Zend_Registry::get('config');
		 try{
              $fullpath=$fileObject->fullpath_createThumbnail($width,$height,$config);
          }
         catch (Exception $ex) {
             //Present Default Image
             $fullpath = $fileObject->getFullPath();
         }

        return $fullpath;
    }
?>