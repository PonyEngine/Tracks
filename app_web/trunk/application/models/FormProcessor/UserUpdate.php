<?
class FormProcessor_UserUpdate extends FormProcessor
{
    protected $db = null;
    protected $_validateOnly = false;
    protected $_apiLogin = false;
    private $_editUserId=null;
    private $_editEmail=null;
    private $_editProfilename=null;
    private $_editBrandNum=null;
    private $_password=null;
    private $_confirmPassword=null;
    private $_expiry=null;
    private $_fpCreated="";


    public function __construct(Zend_Db_Adapter_Abstract $db)
    {
        parent::__construct();
        $this->db = $db;
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


    public function process(Zend_Controller_Request_Abstract $request,$limit=20,$offset=0)
    {
        //Supports JSON and Simple REST
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
            $this->_editUserId=property_exists($phpNative, "editUserId")?$phpNative->editUserId:null;
            $this->_editEmail=property_exists($phpNative, "editEmail")?$phpNative->editEmail:null;
            $this->_editProfilename=property_exists($phpNative, "editProfilename")?$phpNative->editProfilename:null;
            $this->_editBrandNum=property_exists($phpNative, "editBrandNum")?$phpNative->editBrandNum:null;
            $this->_password=property_exists($phpNative, "password")?$phpNative->password:null;
            $this->_confirmPassword=property_exists($phpNative, "confirmPassword")?$phpNative->confirmPassword:null;
            $this->_expiry=property_exists($phpNative, "expiry")?$phpNative->expiry!='Never'?strtotime($phpNative->expiry):'':'';
            $this->_fpCreated=property_exists($phpNative, "createdTo")?$phpNative->createdTo:null;
        }else{
            $this->addError('form','This is only to be used for webform management.');
        }

       is_numeric($this->_editUserId)&& (int)$this->_editUserId>0?$this->_editUserId=(int)$this->_editUserId:$this->addError('editUserId','ID must be a number and greater than 0.');

       if($this->_editBrandNum){ //optional
        is_numeric($this->_editBrandNum)&& (int)$this->_editBrandNum>0?$this->_editBrandNum=(int)$this->_editBrandNum:$this->addError('editBrandNum','Brand Number must be a number and greater than 0.');
        }
        if (!$this->hasError()){
            $db =SavCo_ConstantArr::getDbase(); //why is this not pulling from the instance variable
            $aUser= new DatabaseObject_User($this->db);
            $aUser->load((int)$this->_editUserId);


            if(strlen($this->_editEmail)<1){
                $this->addError('editEmail','Email is Required.');
            }else{
                if($this->_editEmail!= $aUser->usernameEmail){
                    //Check that it does not exist
                    if(DatabaseObject_User::UsernameEmailExists($db,$this->_editEmail)){
                        $this->addError('editEmail','Email already used by another user.');
                    }else{
                        //Save new Email
                        $aUser->usernameEmail=$this->_editEmail;
                        //Send new email for validation
                    }
                }
            }

            if(strlen($this->_editProfilename)<1){
                $this->addError('editProfilename','Profile name is Required.');
            }else{
                if($this->_editProfilename!= $aUser->profileName){
                    if(!DatabaseObject_User::AvailProfileNameAndValid($db,$this->_editProfilename)){
                        $this->addError('editProfilename','Profile name already exists. Choose another.');
                    }else{
                        //Save Profile Name
                        $aUser->profileName=$this->_editProfilenam;
                    }
                }
            }
            //Optional
            if(strlen($this->_expiry)>0){
                if($this->_expiry==false){
                    $this->addError('expiry','Expiration is not valid.');
                }else{
                    if($this->_expiry!=$aUser->expiry){
                        $aUser->expiry=$this->_expiry;
                    }
                }
            }else{
               $aUser->expiry=NULL;
            }

            if($this->_editBrandNum){
                //Only do for members
                if($aUser->user_type=='member'){
                    //Find the current brandnum and delete
                    $brandIds=DatabaseObject_BrandUsers::GetBrandsForUserId($db,$aUser->getId());
                    //Remove User from Old Brands
                    if(count($brandIds)>0){
                       $assocBrand= new DatabaseObject_Brand($db);
                       foreach($brandIds as $aBrandid){
                            $assocBrand->load((int)$aBrandid);
                            $assocBrand->removeAUser($aUser);
                       }
                    }

                    $newAssocBrand= new DatabaseObject_Brand($db);
                    $newAssocBrand->load((int)$this->_editBrandNum);
                     if(!$newAssocBrand->addAUser($aUser))$this->addError('user','Could not add user to brand.');

                }
            }



            //Only check the validity if data is inserted and return error
            //If no selection then just run
            if (!$this->hasError()){
                $userProfileFieldNameIDArr=SavCo_ConstantArr::getUserProfileFieldNameIDArr();
                $aUser->save();
                $this->_fpCreated=sprintf('User %s Saved',$aUser->profileName);
        }
        return !$this->hasError();
        }
    }

    public function created(){
        return $this->_fpCreated;
    }

}

?>