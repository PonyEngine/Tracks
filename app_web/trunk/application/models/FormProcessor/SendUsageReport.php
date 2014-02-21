<?
class FormProcessor_SendUsageReport extends FormProcessor
{
    protected $db = null;
    protected $config=null;
    private $_eventId=null;
    private $_emailAddress=null;

    protected $_sender=null;
    protected $_brand=null;
    protected $_event=null;

    public function __construct($db,DatabaseObject_User $sender)
    {
        parent::__construct();
        $this->db = $db;
        $this->_sender=$sender;
       // $this->config=$config;
    }

    /*
    * USED WITH AJAX CALL
    */
    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }


    public function process(Zend_Controller_Request_Abstract $request)
    {
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_eventId=property_exists($phpNative,"eventId")?$phpNative->eventId:null;
            $this->_emailAddress=property_exists($phpNative, "emailAddress")?$phpNative->emailAddress:null;
        }else{
            $this->_eventId=strlen($request->getParam("eventID"))>0?$request->getParam("eventID"):null;  //Optional
            $this->_emailAddress=strlen($request->getParam("emailAddress"))>0?$request->getParam("emailAddress"):null;  //Optional
        }

        //Check
        if  (strlen($this->_eventId) == 0 )$this->addError('eventId','An event id is required.');
        if  (strlen($this->_emailAddress) == 0 )$this->addError('emailAddress','An email address is required.');

        if (!$this->hasError()){
            $this->sendEmail($this->_eventId,$this->_emailAddress);
        }
        return !$this->hasError();
    }


    public function sendEmail($eventId,$emailAddress){
        $config=Zend_Registry::get('config');
        $event= new DatabaseObject_Event($this->db);

        if($event->load((int)$eventId)){
            $this->_event=$event;

            $brand= new DatabaseObject_Brand($this->db); //Maybe user later
            $brand->load($event->brand_id);
            $this->_brand=$brand;

            //Standard
            $dataArr=array();
            $tpl="usagereport.tpl";
            $html=true;
            $fromName="Event2Pix";

            $dataArr['event']=$this->_event;
            $dataArr['eventStats']=$this->_event->statsPerDay();
            //$dataArr['backgroundColor']="#228b22";
            //$dataArr['backgroundImage']=sprintf("%s/tmp/images/background7.png",$config->webhost);
            //$dataArr['eventFontColor']="#ffffff";
            $dataArr['eventName']=$this->_event->event_name;
            $dataArr['brandName']=$this->_brand->brand_name;
            $dataArr['borderColor']="#f2f2f2";

            $emailFrom=Zend_Registry::get('config')->email->from->email;
            if(SavCo_ZendExtend::Email_FromName_From_To_Data_Template_isHTML($fromName,$emailFrom,$emailAddress,$dataArr,$tpl,$html)){
                //echo "Email has been sent";
            }

        }else{
            $this->addError('event', 'Could not load the event');
        }

        return !$this->hasError();
    }

}

?> 