<?
class FormProcessor_UserSearch extends FormProcessor
{
    protected $db = null;
    public $user = null;

    protected $_validateOnly = false;
    protected $_apiLogin = false;
    private $_config=null;
    private $_userId=null;
    private $_email=null;
    private $_mLevel=null;
    private $_profileName=null;
    private $_brandId=null;
    private $_createdFrom=null;
    private $_createdTo=null;
    private $_lastFrom=null;
    private $_lastTo=null;
    private $_expireFrom=null;
    private $_expireTo=null;
    private $_foundUsers=array();
    private $_fpCreated="";

    public function __construct(Zend_Db_Adapter_Abstract $db, DatabaseObject_User $user,$config)
    {
        parent::__construct();
        $this->db = $db;
        $this->user=$user;
        $this->_config=$config;
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


    public function process(Zend_Controller_Request_Abstract $request,$limit=40,$offset=0)
    {
        //Supports JSON and Simple REST
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_userId=property_exists($phpNative, "userId")?$phpNative->userId:null;
            $this->_email=property_exists($phpNative, "email")?$phpNative->email:null;
            $this->_mLevel=property_exists($phpNative, "mLevel")?$phpNative->mLevel:null;
            $this->_profileName=property_exists($phpNative, "profileName")?$phpNative->profileName:null;
            $this->_brandId=property_exists($phpNative, "brandId")?$phpNative->brandId:null;
            $this->_createdFrom=property_exists($phpNative, "createdFrom")?$phpNative->createdFrom:null;
            $this->_createdTo=property_exists($phpNative, "createdTo")?$phpNative->createdTo:null;
            $this->_lastFrom=property_exists($phpNative, "lastFrom")?$phpNative->lastFrom:null;
            $this->_lastTo=property_exists($phpNative, "lastTo")?$phpNative->lastTo:null;
            $this->_expireFrom=property_exists($phpNative, "expireFrom")?$phpNative->expireFrom:null;
            $this->_expireTo=property_exists($phpNative, "expireTo")?$phpNative->expireTo:null;
        }else{
            $this->addError('form','This is only to be used for webform management.');
        }

        //Only check if info entered
        if($this->_userId){
            is_numeric($this->_userId)&& (int)$this->_userId>0?$this->_userId=(int)$this->_userId:$this->addError('userId','ID must be a number and greater than 0.');
        }

        //Created Time
        if ($this->_createdFrom || $this->_createdTo){
            if ($this->_createdFrom && $this->_createdTo){
                if(!($this->_createdFrom=strtotime($this->_createdFrom)))$this->addError('createdFrom','"Created From" time is not valid.');
                if(!($this->_createdTo=strtotime($this->_createdTo)))$this->addError('createdTo','"Created To" time is not valid.');
            }else{
                $this->addError('createdFrom','Both from and to must be present if searching on creation.');
                $this->addError('createdTo','Both from and to must be present if searching on creation.');
            }
        }

        //Last Sign In
        if ($this->_lastFrom || $this->_lastTo){
            if ($this->_lastFrom && $this->_lastTo){
                if(!($this->_lastFrom=strtotime($this->_lastFrom)))$this->addError('lastFrom','"Last Sign In From" time is not valid.');
                if(!($this->_lastTo=strtotime($this->_lastTo)))$this->addError('lastTo','"Last Sign In To" time is not valid.');
            }else{
                $this->addError('lastFrom','Both from and to must be present if searching on last sign in.');
                $this->addError('lastTo','Both from and to must be present if searching on last sign in.');
            }
        }

        //Expiration
        if ($this->_expireFrom || $this->_expireTo){
            if ($this->_expireFrom && $this->_expireTo){
                if(!($this->_expireFrom=strtotime($this->_expireFrom)))$this->addError('expireFrom','"Expiration From" time is not valid.');
                if(!($this->_expireTo=strtotime($this->_expireTo)))$this->addError('expireTo','"Expiration To" time is not valid.');
            }else{
                $this->addError('expireFrom','Both from and to must be present if searching on expiration.');
                $this->addError('expireTo','Both from and to must be present if searching on exporation.');
            }
        }


        //Only check the validity if data is inserted and return error
        //If no selection then just run
        if (!$this->hasError()){
            $foundUsers=array();
            //$this->_foundUsers=DatabaseObject_User::SelectCondition(0,'');
             $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
            $db =SavCo_ConstantArr::getDbase(); //why is this not pulling from the instance variable

            // Create the Zend_Db_Select object
            $select = $db->select();

            // Add a FROM clause
            $select->from(array('u' => 'users'), array('u.*'));

            // Add a WHERE clause
            if($this->_userId)$select->where('u.user_id= ?', $this->_userId);

            if($this->_email){
                $usernameEmail=$this->_email;
                $select->where("`usernameEmail` LIKE  '%$usernameEmail%' ");
            }

           /* if($this->_brandId){
                $select->join("brands_users","u.user_id=brands_users.user_id");
                $select->where("brands_users.brand_id = ?",(int)$this->_brandId );
            }*/

            if($this->_mLevel)$select->where(sprintf('`user_type` =  "%s"', $this->_mLevel));

            if($this->_profileName){
                $profileName=$this->_profileName;
                $select->where("`profileName` LIKE '%$profileName%'");
            }

            if($this->_createdFrom && $this->_createdTo)$select->where(sprintf('`tsCreated` >= %d AND `tsCreated` <= %d ', $this->_createdFrom,$this->_createdTo));
            if($this->_lastFrom && $this->_lastTo)$select->where(sprintf('`tsLastLogin` >= %d AND `tsLastLogin` <= %d ', $this->_lastFrom,$this->_lastTo));
            if($this->_expireFrom && $this->_expireTo)$select->where(sprintf('`expiry` >= %d AND `expiry` <= %d ', $this->_expireFrom,$this->_expireTo));

            $select->order('u.user_id DESC' );
            $select->limit((int)$limit,(int)$offset);
            $stmt=$db->query($select);
            $rowset=$stmt->fetchAll();

            foreach ($rowset as $row){
                $user_id=(int)$row['user_id'];
                $user=new DatabaseObject_User($db);
                $user->load($user_id);
                $foundUsers[]=$user->arrayRepresentationDetailed($this->_config);
            }
            $this->_foundUsers=$foundUsers;
        }
        return !$this->hasError();
    }

    public function getCreated(){

        $fpCreateMsg='';
        $index=0;
        foreach($this->_fpCreated as $aCreate){
            $fpCreateMsg.=$index>0?' and ':' ';
            $fpCreateMsg.=$aCreate;
            $index++;

        }
        $fpCreateMsg.=count($this->_fpCreated)?" has":" have";
        $fpCreateMsg.=" been created.";

        return $fpCreateMsg;
    }

    public function foundUsers(){
        return $this->_foundUsers;
    }
}

?>