<?php
class DatabaseObject_File_Image extends DatabaseObject_File
{   //general
    protected $_filesSizeMin=100;
    protected $_filesSizeMax=2000000;

    //specific
    protected $_imageMinwidth=20;
    protected $_imageMaxwidth=200;
    protected $_imageMinheight=20;
    protected $_imageMaxheight=200;

    public function __construct($db,$fileDir, $_fileTypeFieldIdName='image_id')
    {
        //;
        $fileDir=sprintf("%s/%s/","images",$fileDir);
        $this->_validExts=explode(',','jpg,png,bmp,jpeg,gif');
        parent::__construct($db,$fileDir,$_fileTypeFieldIdName);

        //$this->_adapter->addValidator('MimeType', false, array('image/gif', 'image/jpeg','image/png'));
        //Causes fileto not be uploaded-->Understand more
        //$this->_adapter->addValidator('IsImage', false);
    }


    public function loadByEventIdFilename($eventId,$filename){
        $db =SavCo_ConstantArr::getDbase(); //why is this not pulling from the instance variable
        $select = sprintf('SELECT image_id FROM %s WHERE  filename="%s" and owner_id=%s', "events_images",
            $filename, (int)$eventId);

        $stmt=$db->query($select);
        $rowset=$stmt->fetchAll();

        if(count($rowset)==1){
            foreach ($rowset as $row){
                $image_id=$row['image_id'];
            }
            return $this->load($image_id);
        }else{
            //error and find out why
            return null;
        }
    }

    public static function GetFileHash($id, $ownerId, $w=0,$h=0,$type='none')
    { //the first number is the id of the file
        //maybe have the next number as the id of the user
        $id = (int) $id;
        $ownerId=(int)$ownerId;
        $w  = (int) $w;
        $h  = (int) $h;
        return sprintf('%d.%d.%s_%s',$id,$ownerId,md5(sprintf('%s,%s,%s', $id,$w,$h)),md5($type));
    }
    public static function GetThumbnailPathWithDirFileHash($type,$fileHash){
        $type_dir_dir=explode('_',$type);
        $thumbPath= sprintf('%s%s%s%s%s', Zend_Registry::get('config')->paths->thumbs,$type_dir_dir[0],DIRECTORY_SEPARATOR,$type_dir_dir[1],DIRECTORY_SEPARATOR.$fileHash);
        return $thumbPath;
    }

    public function fullpath_createThumbnail($maxW, $maxH,$config){
        $fullpath=null;
        try{ //have a default

            $hash=$this->createThumbnail($maxW, $maxH);
        }
        catch (Exception $ex) {
            $fullpath = null; //TODO: Log that useers are getting here on certain data$this->getFullPath();
        }

        $string=sprintf('http://%s/resource/image?&type=%s&id=%d&w=%d&h=%d&hash=%s',
            $config->webhost,
            str_replace('/','_',$this->_drillDownDir),
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
        $fullpath = $this->fullInternalUploadPath($this->getId());

        //Defaults to an image if file does not exist
        if (!file_exists($fullpath)){
            $fullpath=sprintf("%s%s%s",Zend_Registry::get('config')->paths->default,$this->_drillDownDir,"default");
        }
        if (file_exists($fullpath)){
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
            // $path = self::GetThumbnailPath();
            $path = $this->getPathOfThumbnails();

            if (!file_exists($path))
                mkdir($path, 0777);

            // Get the fileHash-- TODO:Correct the hashing here.
            $filename = self::GetFileHash($this->getId(),$this->owner()->getId(), $maxW, $maxH,$this->_fileDir);
            if (!is_writable($path))
                throw new Exception('Unable to write to thumbnail dir'); //Log this

            // determine the full path for the new thumbnail
            $thumbPath = sprintf('%s%s', $path,$filename);

            //echo "New file is $thumbPath";
            if (!file_exists($thumbPath)) {
                //echo "File does not exist<br/>";
                // read the image in to GD
                /* $im = @$infunc($fullpath);
               if (!$im)
                   throw new Exception('Unable to read image file');

               // create the output image
               $thumb = ImageCreateTrueColor($newW, $newH);

               // now resample the original image to the new image
               ImageCopyResampled($thumb, $im, 0, 0, 0, 0, $newW, $newH, $w, $h);
               $outfunc($thumb, $thumbPath);*/

                Asido::driver('GD');
                $i1 = Asido::image($fullpath,$thumbPath);
                /**
                 * Resize it proportionally to make it fit inside a 400x400 frame
                 */
                Asido::resize($i1, $newW, $newH, ASIDO_RESIZE_PROPORTIONAL);

                /**
                 * Save it and overwrite the file if it exists
                 */
                $i1->save(ASIDO_OVERWRITE_ENABLED);

            }

            if (!file_exists($thumbPath))
                throw new Exception('Unknown error occurred creating thumbnail');

            if (!is_readable($thumbPath))
                throw new Exception('Unable to read thumbnail');

        }else{
            null; //If no file then go to default image
        }


        return $filename;
    }

    public static function GetImages($db,$imageOwnerID,$imagesTable,$imagesOwnerIdField,$options = array(),stdClass $class)
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


    public function resizeImage($currentImage,$newImage,$x=0,$y=0,float $width,float $height){
        Asido::driver('GD');

        $i1 = Asido::image($currentImage,$newImage);
        // fit and add white frame
        if($x !=0 || $y!=0){
            Asido::Crop($i1, $x, $y, $width, $height);
            Asido::Frame($i1, $width, $height, Asido::Color(255, 255, 255));
        }
        else{
            Asido::Frame($i1, $width, $height, Asido::Color(255, 255, 255));
        }
        // always convert to jpg
        Asido::convert($i1, 'image/jpg');
        $i1->save(ASIDO_OVERWRITE_ENABLED);
    }

    public function imageMinwidth(){
        return $this->_imageMinwidth;
    }

    public function imageMaxwidth(){
        return $this->_imageMaxwidth;
    }

    public function imageMinheight(){
        return $this->_imageMinheight;
    }
    public function imageMaxheight(){
        return $this->_imageMaxheight;
    }

    public static function GetTempTopImages($db,$limit=10)
    {
        $topImages=array();
        for ($index=1;$index<$limit;$index++){
            $class= get_class();
            $image= new $class($db);
            $image->load($index);
            $topImages[]=$image;
        }
        return $topImages;
    }


}