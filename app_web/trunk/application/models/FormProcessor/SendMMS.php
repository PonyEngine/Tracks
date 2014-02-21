<?
class FormProcessor_SendMMS extends FormProcessor
{
    protected $db = null;
    protected $config=null;
    private $_photoId=null;
    private $_phoneNumber=null;

    protected $_eventImage=null;
    protected $_eventName='';
    protected $_brandName='';
    protected $_brand=null;
    protected $_event=null;

    public function __construct($db,$config)
    {
        parent::__construct();
        $this->db = $db;
        $this->config=$config;
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
            $this->_photoId=$phpNative->photoID;
            $this->_phoneNumber=preg_replace('[\D]', '', $phpNative->phoneNumber);
        }else{
            $this->_photoId=strlen($request->getParam("photoID"))>0?$request->getParam("photoID"):null;  //Optional
            $this->_phoneNumber=strlen($request->getParam("phoneNumber"))>0?preg_replace('[\D]', '', $request->getParam("phoneNumber")):null;  //Optional
        }

        //Check
        if  (strlen($this->_photoId) == 0 )$this->addError('photoId','A photo Id is required.');
        if  (strlen($this->_phoneNumber) != 10 )$this->addError('phoneNumber','Please enter a proper phone number.');

        if (!$this->hasError()){
            $this->sendMMS($this->_photoId,$this->_phoneNumber);
        }
        return !$this->hasError();
    }


    public function sendMMS($photoId,$phoneNumber){
        //All Variables must be local to support calls to this method
        $config=Zend_Registry::get('config');
        $event= new DatabaseObject_Event($this->db);
        $eventImage= new DatabaseObject_File_Image_Event($event);

        if($eventImage->load((int)$photoId)){
            $event->load($eventImage->owner_id);
            $this->_event=$event;

            $brand= new DatabaseObject_Brand($this->db);
            $brand->load($event->brand_id);
            $this->_brand=$brand;

            $this->_eventImage=$eventImage;
            //Standard
            $dataArr=array();
            $tpl="socialsharemail2.tpl";
            $html=true;
            $fromName="Event2Pix";

            $dataArr['eventName']=$this->_event->event_name;
            $dataArr['brandName']=$this->_brand->brand_name;
            $dataArr['fullURL']=sprintf("http://%s/@%s/%s/%s",
                $config->webhost,
                $this->_brand->brand_name,
                preg_replace( '/\s+/', '',$this->_event->event_name),
                $this->_eventImage->filename
            );


             $imgURL=$this->_eventImage->fullpath_createThumbnail(1000, 1000,$config);
             $smsMessage=sprintf("Your photo from %s is ready to share!",$this->_event->event_name);
             $mmsURL= sprintf("https://api.mogreet.com/moms/transaction.send?client_id=%s&token=%s&campaign_id=%s&to=%s&message=%s&content_url=%s",
             $config->mogreet->clientid,
             $config->mogreet->token,
             $config->mogreet->campaignid->mms,
             $phoneNumber,
             urlencode($smsMessage),
             urlencode($imgURL));



            $curlMsg=SavCo_FunctionsGen::RestGETURL($mmsURL);

            //Save Phone Meta Data
            $profileEnum=$this->_eventImage->profileEnum;
            $this->_eventImage->profile->$profileEnum['mmsNumber']=$phoneNumber;


            //Now Process Curl
            if($data = simplexml_load_string($curlMsg)){
                if(property_exists($data, "message_id"))$this->_eventImage->profile->$profileEnum['mmsMessageId']=$data->message_id;
                if(property_exists($data, "hash"))$this->_eventImage->profile->$profileEnum['mmsHash']=$data->hash;
            }

            $this->_eventImage->save();

        }else{
            $this->addError('eventImage', 'Could not load the image for this event');
        }

        return !$this->hasError();
    }



    public function eventPhotoId(){
        return $this->_eventPhotoId;
    }


}

?>