<?php
class DatabaseObject_File extends DatabaseObject
{
    protected static $constId='';
    protected static $constTable='';

    protected $_ownerIdField='';
    protected $_table='';
    protected $_fileDir='';
    protected $_drillDownDir='';

    protected $_owner=null;
    protected $_filesSizeMin=400;
    protected $_filesSizeMax=10000;
    protected $_validExts=array();


    //Major Type Information
    private $_fileTypeFieldIdName;
    protected $_adapter;

    //Directories
    private $_uploadTmpInternal='';  //The private directory of images uploaded to be used for storage of items
    //that will not need to be seen  right away for editing (Music) and should not be placed
    //in public since anyone can access them. This directory will be cleaned
    //regularly
    private $_uploadTmpExternal=''; //The directory where the likes of pictures will be uploaded because they will
    //will need to be seen right away for viewing and editing. This directory will
    // be cleaned regularly.

    private $_uploadFinalInternal=''; //The internal location of where the files will be stored for the owner
    private $_thumbnailPathInt=''; //The internal location of where the files will be stored for the owner
    private $_thumbnailPathExt=''; //The external location of where the files will be stored for the owner
    private $_uploadedFile;
    private $_uploadedFileInternal;
    public function __construct($db,$drillDownDir,$fileTypeFieldIdName='file_id')
    {
        //parent::__construct($db,$this->_databaseTable,$fileTypeFieldIdName);
        parent::__construct($db,$this->_table,$fileTypeFieldIdName);
        $this->add('filename','');
        $this->add($this->_ownerIdField,$this->_owner->getId());//raise error for null
        $this->add('ranking');
        $this->add('privacy',null);
        $this->add('tsCreated',time());
        $this->add('tsModified',null);
        $this->add('tsDeleted',null);

        $this->_uploadTmpExternal= sprintf("%s%s",Zend_Registry::get('config')->paths->tmpextupload,$drillDownDir);
        $this->_uploadTmpInternal= sprintf("%s%s",Zend_Registry::get('config')->paths->tmpintupload,$drillDownDir);
        $this->_uploadFinalInternal=sprintf("%s%s",Zend_Registry::get('config')->paths->upload,$drillDownDir);
        $this->_thumbnailPathInt=sprintf("%s%s",Zend_Registry::get('config')->paths->thumbs,$drillDownDir);
        $this->_thumbnailPathExt=sprintf("%s%s",Zend_Registry::get('config')->paths->thumbsext,$drillDownDir);
        $this->_drillDownDir=$drillDownDir;
    }


    public function loadFirstForOwner()
    {
        $query = sprintf( "select %s from %s where  %s = %d",
            join(', ', $this->getSelectFields()),
            $this->_table,
            $this->_ownerIdField,
            $this->_owner->getId());

        return $this->_load($query);
    }

    public function loadForOwner($owner_id, $file_id)
    {
        $owner_id = (int) $owner_id;
        $file_id = (int) $file_id;

        if ($owner_id <= 0 || $file_id <= 0 ||$this->_table==null)
            return false;

        $query = sprintf( "select %s from %s where  %s = %d and %s = %d",
            join(', ', $this->getSelectFields()),
            $this->_table,
            $this->_ownerIdField,
            $this->_fileTypeFieldIdName,
            $file_id);

        return $this->_load($query);
    }

    public function preInsert()
    {
        $this->populateInfo($this->_uploadedFile);
        // first check that we can write the upload directory
        if (!file_exists($this->_uploadFinalInternal) || !is_dir($this->_uploadFinalInternal)){
            // SavCo_ZendExtend::Log(sprintf("Upload path %s not found",$this->_uploadFinalInternal));
            throw new Exception('Upload path '.$this->e.' not found');
            return false;
        }
        // SavCo_ZendExtend::Log(sprintf("Found upload path %s ",$this->_uploadFinalInternal));

        if (!is_writable($this->_uploadFinalInternal))
            throw new Exception('Unable to write to upload path '.$this->_uploadPath);


        $query = sprintf(
            "select coalesce(max(ranking), 0) + 1 from %s where %s = %d",
            $this->_table,
            $this->_ownerIdField,
            $this->owner()->getId()
        );

        $this->ranking = $this->_db->fetchOne($query);
        $this->filename=$this->getFileNameFromPath($this->_uploadedFile);
        //Finally GetInfo of File
        return true;
    }

    protected function preUpdate()
    {
        $this->tsModified=time();
        return true;
    }

    public function preDelete()
    { 	//Make certain that this works
        unlink($this->fullInternalUploadPath());
        $pattern = sprintf('%s/%d.*',
            self::GetThumbnailPath(),
            $this->getId());

        foreach (glob($pattern) as $thumbnail) {
            unlink($thumbnail);
        }
        return true;
    }


    protected function postInsert()
    {
        $this->moveFromTmpToStorage();
        return true;
    }


    public function insertFileFromURL($url){
        //somehow set the no upload needed for file
        $urlImage=SavCo_FunctionsGen::GetImageFromURL($url);

        if (file_put_contents($this->fullInternalUploadPath($this->_owner->getId()),$urlImage)){

        }
    }

    private function baseClientUpload($newUploadDir,$filePostName){
        $fileInfo='';
        $date 		= md5(time());
        $tmp_loc 	= $_FILES[$filePostName]['tmp_name'];
        $img_parts 	= pathinfo($_FILES[$filePostName]['name']);
        $baseName 	= strtolower($img_parts['basename']);
        $ext = strtolower($img_parts['extension']);
        $new_name 	= sprintf("%s_%s",str_replace("_","-",$baseName),strtolower($date.'.'.$img_parts['extension']));

        $new_uploadfile =sprintf("%s%s",$newUploadDir,$new_name);
        $fileInfo['errors']=array();

        //Checks
        if(!in_array($ext,$this->_validExts)&&strlen($ext)!=0){
            $fileInfo['tmpPath'] = $tmp_loc;
            $fileInfo['filePath'] ='';
            $fileInfo['fileName']=$baseName;
            $fileInfo['errors'][]=sprintf("Only %s files please",implode(',',$this->_validExts));
        }

        if($_FILES[$filePostName]['size'] >=  $this->_filesSizeMax){
            $fileInfo['tmpPath'] = $tmp_loc;
            $fileInfo['filePath'] ='';
            $fileInfo['fileName']=$baseName;
            $fileInfo['errors'][]=sprintf("File exceeds the upload size of %s MB",SavCo_FunctionsGen::Format_bytes($this->_filesSizeMax));
            //Check if directory exists and if not then add it
        }

        if(count($fileInfo['errors'])==0){
            if(!file_exists($newUploadDir)){
                mkdir($newUploadDir,0755,true);
            }

            if (move_uploaded_file($tmp_loc, $new_uploadfile)) {
                // add key value to arr
                $fileInfo['tmpPath'] = $tmp_loc;
                $fileInfo['filePath'] = str_replace("../www","",$new_uploadfile);
                $fileInfo['fileName']=$baseName;
                if(file_exists($tmp_loc)){
                    // unlink($tmp_loc);
                }
            }

        }

        return $fileInfo;
    }

    public function uploadFromClientToExternalTmp($filePostName){
        return $this->baseClientUpload($this->_uploadTmpExternal,$filePostName);
    }

    public function uploadFromClientToInternalTmp($filePostName){
        return $this->baseClientUpload($this->_uploadTmpInternal,$filePostName);
    }

    private function moveFromTmpToStorage(){
        $storage_loc= $this->fullInternalUploadPath($this->getId());
        if (file_exists($this->_uploadedFile) && is_file($this->_uploadedFile)){
            if (file_exists($this->_uploadFinalInternal)){
                if (rename($this->_uploadedFile,$storage_loc )) {
                    if(file_exists($this->_uploadedFile)){
                        unlink($this->_uploadedFile);
                    }
                    return true;
                }else{
                    throw new Exception(sprintf("Destination does not exist or path is not a file"));
                }
            }else{
                SavCo_ZendExtend::Log("Could not find the file","warning");
                throw new Exception(sprintf("File does not exist or path is not a file"));
            }
        }
    }

    public function moveFromExternalTmpToStorage(){
        $tmp_loc=$this->fullExternalTmpPath(); //could also let it check automatically to see where file is
        $storage_loc= $this->fullInternalUploadPath();
        if (move_uploaded_file($tmp_loc,$storage_loc )) {
            // add key value to arr
            $arr['temp_uploadfile'] =$tmp_loc;
            $arr['new_uploadfile'] = $storage_loc;
            unlink($tmp_loc);
            return $arr;
        }
    }

    /* public function processExtTmpFile($filePath){
        $idIs=$this->getId();
        $tmp_loc='../www'.$filePath; //will  begin at www directory
        $storage_loc= $this->fullInternalUploadPath().$this->getId();
        if(file_exists($tmp_loc)){
            if(file_exists($this->fullInternalUploadPath())){
            if (move_uploaded_file($tmp_loc,$storage_loc )) {
                unlink($tmp_loc);
                return true;
            }
            }
        }
        return false;
    }

    public function processIntTmpFile($fileName){
        $tmp_loc=$this->fullInternalTmpPath().$fileName; //will  begin at www directory
        $storage_loc= $this->fullInternalUploadPath().basename($fileName);
        if(file_exists($tmp_loc)){
            if(file_exists($this->fullInternalUploadPath())){
                if (move_uploaded_file($tmp_loc,$storage_loc )) {
                    unlink($tmp_loc);
                    return true;
                }
            }
        }
        return false;
    }
    */
    /*public static function GetUploadPath($uploadDir)
    {
        $config = Zend_Registry::get('config');
        return sprintf('%s/uploaded-files/'.$uploadDir, $config->paths->data);
    }*/

    public static function GetThumbnailPath()
    {
        $config = Zend_Registry::get('config');
        return sprintf('%s/tmp/images', $config->paths->data);
    }

    public static function GetThumbnailPathWithDirFileHash($type,$fileHash){
        $type_dir_dir=explode('_',$type);
        $thumbPath= sprintf('%s%s%s%s%s', Zend_Registry::get('config')->paths->thumbs,$type_dir_dir[0],DIRECTORY_SEPARATOR,$type_dir_dir[1],DIRECTORY_SEPARATOR.$fileHash);
        return $thumbPath;
    }

    public function fullExternalTmpPath($fileName='')
    {
        return sprintf('%s%s', $this->_uploadTmpExternal,$fileName);
    }

    public function fullInternalTmpPath($fileName='')
    {
        return sprintf('%s%s', $this->_uploadTmpInternal,$fileName);
    }

    public function fullInternalUploadPath($fileName='')
    {
        if (!file_exists($this->_uploadFinalInternal))
            mkdir($this->_uploadFinalInternal);

        return sprintf('%s%s', $this->_uploadFinalInternal,$fileName);
    }

    public function validExtensions(){
        return $this->_validExts;
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

    public static function SmartReadFile($location, $filename, $mimeType='application/octet-stream')
    {
        if(!file_exists($location)) {header ("HTTP/1.0 404 Not Found");
            return;
        }
        $size=filesize($location);
        $time=date('r',filemtime($location));
        $fm=@fopen($location,'rb');

        if(!$fm)
        { header ("HTTP/1.0 505 Internal server error");
            return;
        }
        $begin=0;
        $end=$size;

        if(isset($_SERVER['HTTP_RANGE']))
        {
            if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
            {
                $begin=intval($matches[0]);

                if(!empty($matches[1]))
                    $end=intval($matches[1]);
            }

        }

        if($begin>0||$end<$size)
            header('HTTP/1.0 206 Partial Content');
        else
            header('HTTP/1.0 200 OK');
        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:'.($end-$begin));
        header("Content-Range: bytes $begin-$end/$size");
        header("Content-Disposition: inline; filename=$filename");
        header("Content-Transfer-Encoding: binary\n");
        header("Last-Modified: $time");
        header('Connection: close');

        $cur=$begin;
        fseek($fm,$begin,0);

        while(!feof($fm)&&$cur<$end&&(connection_status()==0))
        {
            print fread($fm,min(1024*16,$end-$cur));
            $cur+=1024*16;
        }
    }

    public function owner(){
        return $this->_owner;
    }

    public static function Table(){
        $debugTable=DatabaseObject_File::$Table;
        return DatabaseObject_File::$Table;
    }

    public static function GetOwnerIdField(){
        $ownerIdField=DatabaseObject_File::$OwnerIdField;
        return $ownerIdField;
    }

    public static function GetOwnerTable(){
        $ownerTable=DatabaseObject_File::$OwnerTable;
        return $ownerTable;
    }

    public function saveFile($filePath, $isInternal=true){
        $baseName=basename($filePath);
        $this->_uploadedFile=($isInternal)?$this->fullInternalTmpPath($baseName): $this->fullExternalTmpPath($baseName);
        if (!file_exists($this->_uploadedFile)){
            SavCo_ZendExtend::Log(sprintf("File %s does not exist",$this->_uploadedFile));
            return false;
        }
        //Make certain directory is available
        if (!file_exists($this->_uploadFinalInternal)){
            mkdir($this->_uploadFinalInternal);
            SavCo_ZendExtend::Log("Image:Making Directories");
        }else{
            null;
        }

        try {
            return parent::save();
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    private function getFileNameFromPath($filePath){
        $baseName=basename($filePath);
        $baseExplode=explode('-',$baseName);
        return $baseExplode[0];
    }
    public function getPathOfThumbnails($internal=true){
        return $internal?$this->_thumbnailPathInt:$this->_thumbnailPathExt;
    }


    /*public static function BuildMultiple(DatabaseObject $ownerObj, $class, $data)
    {
        $ret = array();

        if (!class_exists($class))
            throw new Exception('Undefined class specified: ' . $class);

        $testObj = new $class($ownerObj);

        if (!$testObj instanceof DatabaseObject)
            throw new Exception('Class does not extend from DatabaseObject');

        foreach ($data as $row) {
            $obj = new $class($ownerObj);
            $obj->_init($row);

            $ret[$obj->getId()] = $obj;
        }

        return $ret;
    }*/

    public static function BuildMultiple(DatabaseObject $ownerObj, $class, $data, $isFullObj=true)
    {
        $ret = array();

        if (!class_exists($class))
            throw new Exception('Undefined class specified: ' . $class);

        $testObj = new $class($ownerObj);

        if (!$testObj instanceof DatabaseObject)
            throw new Exception('Class does not extend from DatabaseObject');

        foreach ($data as $row) {
            $obj = new $class($ownerObj);

            if ($isFullObj){
                $obj->load($row['file_id']);
            }else{
                $obj->_init($row);
            }
            $ret[$obj->getId()] = $obj;
        }

        return $ret;
    }

    protected function populateInfo(){

    }

}