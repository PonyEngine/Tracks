<?php
class FormProcessor_File extends FormProcessor
{	protected $dbObject;
    protected $post;
    protected $_adapter;
    private $_request;
    private $_isInternalTmp=true;
    private $_fileInfoOfUploaded=null;
    private $_filePostName;

    public function __construct(DatabaseObject_File $aDBObject, $theFilePostName="",$isInternalTmp=true)
    {
        parent::__construct();
        $this->_adapter = new Zend_File_Transfer_Adapter_Http();
        $this->dbObject = $aDBObject;
        $this->_filePostName=$theFilePostName;
        $this->_isInternalTmp=$isInternalTmp;
    }

    function process(Zend_Controller_Request_Abstract $request)
    {
        $fileInfo=null;
        $this->_adapter->addValidator('Extension', false, $this->dbObject->validExtensions());
        if ($this->_isInternalTmp){
            $this->_fileInfoOfUploaded=$this->processUploadToTmpInternal();
        }else{
            $this->_fileInfoOfUploaded=$this->processUploadToTmpExternal();
            SavCo_ZendExtend::Log("Image Uploading Externally");
        }
        $index=0;
        foreach($this->_fileInfoOfUploaded['errors'] as $aFileError){
            $this->addError("file_$index",$aFileError);
            $index++;
        }
        // SavCo_ZendExtend::Log()
        return !$this->hasError();
    }

    private function  processUploadToTmpInternal(){
        return $this->_fileInfoOfUploaded=$this->dbObject->uploadFromClientToInternalTmp($this->_filePostName);
    }

    private function  processUploadToTmpExternal(){
        return $this->_fileInfoOfUploaded=$this->dbObject->uploadFromClientToExternalTmp($this->_filePostName);
    }
    public function fileInfoOfUploaded(){
        return $this->_fileInfoOfUploaded;
    }

    public function uploadMsg(){
        return "";
    }
}
