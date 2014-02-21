<?
class FormProcessor_SocialSettingsUpdate extends FormProcessor
{
    protected $db = null;
    public $user = null;

    protected $_validateOnly = false;
    protected $_apiLogin = false;
    private $_eventId=null;
    private $_updatePage=false;
    private $_updateSocialNetworks=false;
    private $_updateEmail=false;
    private $_updateMMS=false;
    private $_emailBackgrounds=array();
    private $_emailMsg=null;
    private $_templateEmail=null;

    private $_fpUpdated='';

    public function __construct(Zend_Db_Adapter_Abstract $db, DatabaseObject_User $user)
    {
        parent::__construct();
        $this->db = $db;
        $this->user=$user;
    }

    /*
    * USED WITH AJAX CALL
    */
    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }

    /*
    * ESTABLISHED MOBILE AWARENESS
    * BECAUSE SESSIONS ARE HANDLED DIFFRENTLY (STATELESS)
    */
    public function apiLogin($flag){
        $this->_apiLogin=(bool)$flag;
        $this->_clearIdentity=(bool)!$flag;
    }


    public function process(Zend_Controller_Request_Abstract $request)
    {
        //Supports JSON and Simple REST
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_eventId=property_exists($phpNative, "eventId")?$phpNative->eventId:null;
            $this->_updatePage=property_exists($phpNative, "updatePage")?$phpNative->updatePage:null;
            $this->_updateSocialNetworks=property_exists($phpNative, "updateSocialNetworks")?$phpNative->updateSocialNetworks:null;
            $this->_updateEmail=property_exists($phpNative, "updateEmail")?$phpNative->updateEmail:null;
            $this->_updateMMS=property_exists($phpNative, "updateMMS")?$phpNative->updateMMS:null;
//            $this->_templateEmail=property_exists($phpNative, "templateEmail")?$phpNative->templateEmail:null;

            if($this->_updatePage){ //Capture Page info if set

            }

            if($this->_updateSocialNetworks){ //Capture social networks
            }

            if($this->_updateEmail){ //Capture email info needed
                $this->_emailMsg=property_exists($phpNative, "emailMsg")?$phpNative->emailMsg:null;

                //Assets
                if (property_exists($phpNative, "imgEmailBGArray")){
                    foreach($phpNative->imgEmailBGArray as $imgPath){
                        $this->_emailBackgrounds[]=$imgPath;
                    }
                }
                //Error check cleaner here
               // if (strlen($this->_emailMsg) == 0)$this->addError('emailMsg','No Message to input. A message must be present'); // May be optional
               // if (count($this->_emailBackgrounds)==0)$this->addError("imgEmailBGArray","an imagebackground is required");
            }

            if($this->_updateMMS){//Capture mms info

            }
        }else{
            $this->addError('form','This is only to be used for webform management.');
        }

        if(!$this->hasError()){
            $event= new DatabaseObject_Event($this->db);
            $profileEnum=$event->profileEnum;
            if ($event->load((int)$this->_eventId)){

                //if($this->_updateEmail){
  //                $event->updateTemplateEmail($this->_templateEmail);

                //}


                $brand= new DatabaseObject_Brand($this->db);
                if($brand->load($event->brand_id)){
                    //Save Email BG


                    if($this->_updateEmail){ //Do this only for email right now
                        foreach ($this->_emailBackgrounds as $anImgPath){
                            if (strlen($anImgPath)>0){
                                $aBrandImg= new DatabaseObject_File_Image_Brandimgasset($brand);
                                $aBrandImg->assetType=$profileEnum['brandimgmemailbgId'];
                                if($aBrandImg->saveFile($anImgPath,false)){ //process only if file is saved
                                    $event->profile->$profileEnum['brandimgmemailbgId']=$aBrandImg->getId();
                                }
                            }
                        }

                        $event->profile->$profileEnum['emailMsg']=$this->_emailMsg;
                        $event->save();
                    }
                }else{
                    $this->addError("brand", "Brand  does not exist");
                }
            }else{
                $this->addError("event", sprintf("Event %s does not exist",$this->_eventId));
            }
        }

        return !$this->hasError();
    }

    public function getUpdated(){
        $fpUpdateMsg='';
        $index=0;
        foreach($this->_fpUpdated as $anUpdate){
            $fpUpdateMsg.=$index>0?' and ':' ';
            $fpUpdateMsg.=$anUpdate;
            $index++;

        }
        $fpUpdateMsg.=count($this->_fpUpdated)?" has":" have";
        $fpUpdateMsg.=" been updated.";

        return $fpUpdateMsg;
    }
}

?> 