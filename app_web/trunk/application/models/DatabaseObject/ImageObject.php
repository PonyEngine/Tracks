<?php
    class DatabaseObject_ImageObject extends DatabaseObject
    {
        protected $_uploadedFile;
		protected $_imageOwnerID='';
		protected $_uploadDir='uploaded-files';
		protected $_imagesTable='';
		protected $_imageOwnerIDVal=0;
        public function __construct($db,$imagesTable,$imageOwnerID)
        {
          parent::__construct($db,$imagesTable,'image_id' );
		  $this->_imageOwnerID=$imageOwnerID;
		  $this->_imagesTable=$imagesTable; 
		  $this->add('filename');
		  $this->add($imageOwnerID);
		  $this->add('descriptionJson');
          $this->add('ranking');
		   
        }

        public function preInsert()
        {
            // first check that we can write the upload directory
            $path = self::GetUploadPath($this->_uploadDir);
            if (!file_exists($path) || !is_dir($path))
                throw new Exception('Upload path ' . $path . ' not found');

            if (!is_writable($path))
                throw new Exception('Unable to write to upload path ' . $path);

            // now determine the ranking of the new image
            $query = sprintf(
                'select coalesce(max(ranking), 0) + 1 from %s where '.$this->_imageOwnerID.' = %d',
                $this->_table,
                $this->__get($this->_imageOwnerID)
			);
			
            $this->ranking = $this->_db->fetchOne($query);
            return true;
        }

        public function preDelete()
        {
            unlink($this->getFullPath());

            $pattern = sprintf('%s/%d.*',
                               self::GetThumbnailPath(),
                               $this->getId());

            foreach (glob($pattern) as $thumbnail) {
                unlink($thumbnail);
            }

            return true;
        }

        public function postInsert()
        {
            if (strlen($this->_uploadedFile) > 0)
                return move_uploaded_file($this->_uploadedFile,
                                          $this->getFullPath());

            return false;
        }

        public function loadImage($imageOwnerID, $image_id)
        {
            $imageOwnerID = (int) $imageOwnerID;
            $image_id = (int) $image_id;

            if ($imageOwnerID <= 0 || $image_id <= 0)
                return false;

            $query = sprintf(
                'select %s from %s where '.$imageOwnerID.'= %d and image_id = %d',
                join(', ', $this->getSelectFields()),
                $this->_table,
                $imageOwnerID,
                $image_id
            );

            return $this->_load($query);
        }

        public function uploadFile($path)
        {
            if (!file_exists($path) || !is_file($path))
                throw new Exception('Unable to find uploaded file');

            if (!is_readable($path))
                throw new Exception('Unable to read uploaded file');

            $this->_uploadedFile = $path;
        }

        public function getFullPath()
        {
            return sprintf('%s/%d', self::GetUploadPath($this->_uploadDir), $this->getId());
        }

        public function createThumbnail($maxW,$maxH,$type)
        {
            $fullpath = $this->getFullpath();
			
			
            $ts = (int) filemtime($fullpath);
            $info = getImageSize($fullpath);
	
          
	
            $w = $info[0];          // original width
            $h = $info[1];          // original height

            $ratio = $w / $h;       // width:height ratio

            $maxW = min($w, $maxW); // new width can't be more than $maxW
            if ($maxW == 0)         // check if only max height has been specified
                $maxW = $w;

            $maxH = min($h, $maxH); // new height can't be more than $maxH
            if ($maxH == 0)         // check if only max width has been specified
                $maxH = $h;

            $newW = $maxW;          // first use the max width to determine new
            $newH = $newW / $ratio; // height by using original image w:h ratio

            if ($newH > $maxH) {        // check if new height is too big, and if
                $newH = $maxH;          // so determine the new width based on the
                $newW = $newH * $ratio; // max height
            }

            if ($w == $newW && $h == $newH) {
                // no thumbnail required, just return the original path
                return $fullpath;
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

                default;
                    throw new Exception('Invalid image type');
            }

            // create a unique filename based on the specified options
            $filename = sprintf('%d.%dx%d.%d.%s',
                                $this->getId(),
                                $newW,
                                $newH,
                                $ts,md5($type));

            // autocreate the directory for storing thumbnails
            $path = self::GetThumbnailPath();
			
            if (!file_exists($path))
                mkdir($path, 0777);

            if (!is_writable($path))
                throw new Exception('Unable to write to thumbnail dir');

            // determine the full path for the new thumbnail
            $thumbPath = sprintf('%s/%s', $path, $filename);

            if (!file_exists($thumbPath)) {

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

            return $thumbPath;
        }

        public static function GetImageHash($id, $w, $h, $type)
        {
            $id = (int) $id;
            $w  = (int) $w;
            $h  = (int) $h;
			
			
            return md5(sprintf('%s,%s,%s,%s',$id,$w,$h,$type));
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

        public static function GetImages($db,$imageOwnerID,$imagesTable,$options = array(),$class)
        {
            // initialize the options
            $defaults = array($imageOwnerID => array());
			

            foreach ($defaults as $k => $v) {
                  $options[$k] = array_key_exists($k, $options) ? $options[$k] : $v;
            }
				
            $select = $db->select();
            $select->from(array('i' => $imagesTable), array('i.*'));

            // filter results on specified user ids (if any)
            if (count($options[$imageOwnerID]) > 0)
                $select->where('i.'.$imageOwnerID.' in (?)', $options[$imageOwnerID]);

            $select->order('i.ranking');

			
             // fetch post data from database
             $data = $db->fetchAll($select);
			  // turn data into array of ImageObject_User objects
			$images=parent::BuildMultiple($db,$class, $data);
			return $images;
        }
    

/**
 * Returns $_imageOwnerID.
 *
 * @see DatabaseObject_ImageObject::$_imageOwnerID
 */
public function get_imageOwnerID() {
    return $this->_imageOwnerID;
}
}
?>