<?php
    class DatabaseObject_File_Audio extends DatabaseObject_File
    {	//general
		protected $_sizeMin=100;
	    protected $_sizeMax=2000000;  
		
		protected $_extensions_Validator=Array();
		

        public function __construct($db,$ownerIdField,$databaseTable,$fileDirectory,$filePostName='',$ownerId)
        {
            $_fileTypeFieldIdName='audio_id';
            parent::__construct($db,$ownerIdField,$databaseTable,$fileDirectory,$filePostName,$ownerId,$this->_extensions_Validator,$_fileTypeFieldIdName);

            //Add Specific Validators Here
           /* $this->_adapter->addValidator('ImageSize', false,array('minwidth' => $this->_imageMinwidth,
                'maxwidth' => $this->_imageMaxwidth,
                'minheight' => $this->_imageMinheight,
                'maxheight' => $this->_imageMaxheight));*/

            $this->_adapter->addValidator('Extension', false, array('mp3'));

            //$this->_adapter->addValidator('MimeType', false, array('image/gif', 'image/jpeg','image/png'));
            //Causes fileto not be uploaded-->Understand more
            //$this->_adapter->addValidator('IsImage', false);
        }

        public function fullpath_createThumbnail($maxW, $maxH,$config){
            $fullpath=null;

            try{ //have a default

                $hash=$this->createThumbnail($maxW, $maxH);
            }
            catch (Exception $ex) {
                $fullpath = $this->getFullPath();
            }

            $string=sprintf('http://%s/resource/image?&type=%s&id=%d&w=%d&h=%d&hash=%s',
                $config->webhost,
                $this->getFileDirectory(),
                $this->getId(),
                $maxW,
                $maxH,
                $hash);
            $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
            $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
            $fullpath=str_replace($entities, $replacements, urlencode($string));;

            return $fullpath;
        }

        public function createThumbnail($maxW, $maxH)
        {
            $fullpath = $this->getFullPath();
            if (!file_exists($fullpath)){
                $fullpath=sprintf("%s%s%s",Zend_Registry::get('config')->paths->default,'images'.DIRECTORY_SEPARATOR,$this->_fileDirectory);
            }
            $ts = (int) filemtime($fullpath);
            $info = getImageSize($fullpath);
            $w = $info[0];          // original width
            $h = $info[1];          // original height

            //SavCO Kludge- prevents division by 0
            $h=$h>0?$h:1;

            $ratio = $w / $h;       // width:height ratio
            $maxW = min($w, $maxW); // new width can't be more than $maxW

            if ($maxW == 0)         // check if only max height has been specified
                $maxW = $w;
            $maxH = min($h, $maxH); // new height can't be more than $maxH

            if ($maxH == 0)         // check if only max width has been specified
                $maxH = $h;
            $newW = $maxW;          // first use the max width to determine new

            //SavCO Kludge- prevents division by 0
            $ratio=$ratio>0?$ratio:1;

            $newH = $newW / $ratio; // height by using original image w:h ratio
            if ($newH > $maxH) {        // check if new height is too big, and if
                $newH = $maxH;          // so determine the new width based on the
                $newW = $newH * $ratio; // max height
            }
            switch ($info[2]) {
                case IMAGETYPE_GIF:
                    $infunc = 'ImageCreateFromGif';
                    $outfunc = 'ImageGif';
                    break;

                case IMAGETYPE_JPEG:
                    $infunc = 'ImageCreateFromJpeg';
                    $outfunc = 'ImageJpeg';
                    break;

                case IMAGETYPE_PNG:
                    $infunc = 'ImageCreateFromPng';
                    $outfunc = 'ImagePng';
                    break;
                default:
                    throw new Exception('Invalid image type');
                    break;
            }

            // autocreate the directory for storing thumbnails
            $path = self::GetThumbnailPath();

            if (!file_exists($path))
                mkdir($path, 0777);

            // Get the fileHash-- TODO:Correct the hashing here.
            $filename = self::GetFileHash($this->getId(),$this->user_id, $maxW, $maxH,$this->_fileDirectory);
            if (!is_writable($path))
                throw new Exception('Unable to write to thumbnail dir'); //Log this

            // determine the full path for the new thumbnail
            $thumbPath = sprintf('%s%s%s', $path.DIRECTORY_SEPARATOR,$this->_fileDirectory.DIRECTORY_SEPARATOR,$filename);

            //echo "New file is $thumbPath";
            if (!file_exists($thumbPath)) {
                //echo "File does not exist<br/>";
                // read the image in to GD
                $im = @$infunc($fullpath);
                if (!$im)
                    throw new Exception('Unable to read image file');

                // create the output image
                $thumb = ImageCreateTrueColor($newW, $newH);

                // now resample the original image to the new image
                ImageCopyResampled($thumb, $im, 0, 0, 0, 0, $newW, $newH, $w, $h);
                $outfunc($thumb, $thumbPath);
            }

            if (!file_exists($thumbPath))
                throw new Exception('Unknown error occurred creating thumbnail');

            if (!is_readable($thumbPath))
                throw new Exception('Unable to read thumbnail');
            return $filename;
        }

        public static function GetFileHash($id, $ownerId, $w=0,$h=0,$type='none')
            //maybe have the next number as the id of the user
        { //the first number is the id of the file
            $id = (int) $id;
            $ownerId=(int)$ownerId;
            $w  = (int) $w;
            $h  = (int) $h;
            return sprintf('%d.%d.%s_%s',$id,$ownerId,md5(sprintf('%s,%s,%s', $id,$w,$h)),md5($type));
        }


        public static function GetUploadPath($uploadDir)
        {
            $config = Zend_Registry::get('config');
            return sprintf('%s/uploaded-files/'.$uploadDir, $config->paths->data);
        }

        public static function GetThumbnailPath()
        {
            $config = Zend_Registry::get('config');
            return sprintf('%s/tmp/images', $config->paths->data);
        }

        public static function GetThumbnailPathWithDirFileHash($type,$fileHash){
            return sprintf('%s%s%s', Zend_Registry::get('config')->paths->cache.'/images/',$type,DIRECTORY_SEPARATOR.$fileHash);

        }

        public static function GetImages($db,$imageOwnerID,$imagesTable,$imagesOwnerIdField,$options = array(),$class)
        {
            $defaults = array($imageOwnerID => array());

            foreach ($defaults as $k => $v) {
                $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }

            $select = $db->select();
            $select->from(array('i' => $imagesTable), array('i.*'));

            // filter results on specified user ids (if any)
            //if (count($options[$imageOwnerID]) > 0)
            // $select->where('i.'.$imageOwnerID.' in (?)', $options[$imageOwnerID]);

            $theOwnerField=$imagesOwnerIdField;

            $select->where("$theOwnerField = $imageOwnerID");
            $select->order('i.ranking');

            // fetch post data from database
            $data = $db->fetchAll($select);
            // turn data into array of ImageObject_User objects
            $images=parent::BuildMultiple($db,$class, $data);
            return $images;
        }


        public function trimAudio($arr,$filePostName){
            //you can change the name of the file here
            $date 		= md5(time());
            //////////// upload image and resize
            $uploaddir 	= $arr['uploaddir'];
            $tempdir	= $arr['tempdir'];
            $temp_name 	= $_FILES[$filePostName]['tmp_name'];
            $img_parts 	= pathinfo($_FILES[$filePostName]['name']);
            $baseName 	= strtolower($img_parts['basename']);
            $new_name 	= sprintf("%s_%s",str_replace("_","-",$baseName),strtolower($date.'.'.$img_parts['extension']));
            //$new_name=sprintf("%d.%s",$this->_ownerId,basename($_FILES['photo']['name']));
            $ext = strtolower($img_parts['extension']);
            $allowed_ext = array('mp3','wav','acc');


            $temp_uploadfile = $tempdir.$new_name;
            $new_uploadfile = $uploaddir.$new_name;

            if(!in_array($ext,$allowed_ext)&&strlen($ext)!=0){
                echo "<p class='uperror'>Please upload again. Only MP3s, WAV and ACC files please. $ext is not valid$new_uploadfile</p>";
                exit;
            }


            if($_FILES[$filePostName]['size'] <  209700000 ){
                if (move_uploaded_file($temp_name, $new_uploadfile)) {
                    // add key value to arr
                    $arr['temp_uploadfile'] = $temp_uploadfile;
                    $arr['new_uploadfile'] = $new_uploadfile;
                    $arr['filename']=$new_name;
                    unlink($temp_uploadfile);
                    return $arr['filename'];
                }
            }
            else{
                echo '<p class="uperror">Please upload again. Maximum filesize is 1.3MB.</p>';
                exit;
            }
        }

        public function streamdownloadAudio(){
            $dir = dirname($_SERVER['DOCUMENT_ROOT'])."/data/uploaded-files/audio/tracks";
            //playlistItemId
             //Chek that the user can play this track
            //Get Track from playlistItemId
            //Get filename form TrackId

            $filename = $_GET['file'];
            $filename="1.mp3";

            $file = $dir."/".$filename;

            $extension = "mp3";
            $mime_type = "audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3";

            if(file_exists($file)){
                header('Content-type: {$mime_type}');
                header('Content-length: ' . filesize($file));
                header('Content-Disposition: filename="' . $filename);
                header('X-Pad: avoid browser bug');
                header('Cache-Control: no-cache');
                readfile($file);
            }else{
                header("HTTP/1.0 404 Not Found");
            }
        }

        public function  streamAudio(){

        }

    }
?>