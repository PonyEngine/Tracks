<?php
class FormProcessor_File_Audio extends FormProcessor_File
{
    protected $audio;
    public function __construct(DatabaseObject $aDBObject,$theFilePostName)
    {
        parent::__construct($aDBObject,$theFilePostName);
    }

    public function uploadToTmp(Zend_Controller_Request_Abstract $request){
        $config=Zend_Registry::get('config');
        $big_arr = array(
            'uploaddir'	=> $config->atemp->audioServer,
            'tempdir'	=> $config->atemp->audio,
            'length'	=> $request->getPost('length'),
            'crop'     => false);


        $fileInfo['fileName']=$this->audio->trimAudio($big_arr,$this->filePostName);
        $explodedFileName=explode('_',$fileInfo['fileName']);
        $fileInfo['audioName']=$explodedFileName[0];
        $fileInfo['filePath']= Zend_Registry::get('config')->atemp->audioURL. $fileInfo['fileName'];
        return $fileInfo;
    }

    public function processUploadedFile($pathOfTmpFile){
        $imgName= str_replace($this->dbObject->getId().'.','',basename($pathOfTmpFile));

        if (!$this->hasError()) {
            $imgFileParts=explode('_',basename($pathOfTmpFile));
            $this->audio->filename =$imgFileParts[0];

            $this->audio->save();
            $audioId=$this->audio->getId();
            $uploadRPath=sprintf("%s%s",$this->audio->getPathDir(),DIRECTORY_SEPARATOR.$audioId);

            $returnSize=40;
            $fileDir=$this->image->getFileDirectory();
            $fileName=$this->image->createThumbnail($returnSize,$returnSize);
            return urlencode($filePath="/resource/image?&type=$fileDir&id=$audioId&w=40&h=40&hash=$fileName");
        }
    }

}
?>