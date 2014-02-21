<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 6/7/13
 * Time: 7:39 PM
 * To change this template use File | Settings | File Templates.
 */
class FormProcessor_UserLeaders extends FormProcessor
{
    protected $db = null;
    protected $_config=null;
    protected $_user=null;
    protected $_leaders=null;
    private $_filterType=null;
    private $_limit=null;
    private $_offset=null;
    private $_lastId=null;
    private $_lat=null;
    private $_lon=null;

    public function __construct(DatabaseObject_User $user,$config)
    {
        parent::__construct();
        $this->db = $user->getDb();
        $this->_config=$config;
        $this->_user=$user;
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
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_filterType=$phpNative->filterType;
            $this->_limit=$phpNative->limit;
            $this->_offset=$phpNative->offset;
            $this->_lastId=$phpNative->lastId?$phpNative->lastId:0;
            $this->_lat=$phpNative->lat;
            $this->_lon=$phpNative->lon;
        }else{
            $this->_filterType=$request->getParam("filterType");
            $this->_limit=$request->getParam("limit");
            $this->_offset=$request->getParam("offset");
            $this->_lastId=$request->getParam("lastId")?$request->getParam("lastId"):0;
            $this->_lat=$request->getParam("lat");
            $this->_lon=$request->getParam("lon");
        }

        //Optional
        if($this->_limit){
            if((int)$this->_limit<=0)$this->addError('limit','Limit must be greater than 0 if set.');
        }else{
            $this->_limit=100;
        }

        if($this->_offset){
            if((int)$this->_offset<=-1)$this->addError('offset','Offset must be greater than -1 if set.');
        }else{
            $this->_offset=0;
        }
        if (!$this->hasError()){
            $this->_leaders=array();
            $leaders=DatabaseObject_User::UsersArrayRepresentationForFilterType($this->_user,$this->_filterType,$this->_config,$this->_limit,$this->_offset,$this->_lastId,$this->_lat,$this->_lon);

            foreach($leaders as $aLeader){
                $theLeader['userId']=(int)$aLeader['user_id'];
                $theLeader['usernameEmail']=$aLeader['usernameEmail'];
                $theLeader['profileName']=$aLeader['profileName'];
                $theLeader['user_type']=$aLeader['user_type'];
                $theLeader['bucks']=$aLeader['bucks'];
                $theLeader['points']=$aLeader['points'];
                $theLeader['level']=$aLeader['level'];

                $user= new DatabaseObject_User($this->_user->getDb());
                $user->load((int)$aLeader['user_id']);
                $theLeader['picURL']=$user->picURL($this->_config);
                $theLeader['fullName']=sprintf("%s %s",$user->profile->up1,$user->profile->up3);
                $this->_leaders[]=$theLeader;
            }
        }
        return !$this->hasError();
    }

    public function leaders(){
        return $this->_leaders;
    }
}

?>