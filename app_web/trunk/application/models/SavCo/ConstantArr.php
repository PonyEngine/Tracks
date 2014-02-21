 <?
	class SavCo_ConstantArr extends SavCo{	
	  private static $frontendOpts = array('caching'=>true,'lifetime' =>1800, 'automatic_serialization' =>true); 
	  private static $backendOpts = array('servers' => array('host' =>'66.147.254.19','port' =>11211),'compression' =>false);			
		
	 function __construct(){
		parent::__construct();
		$this->db = Zend_Registry::get('db');
	}

    /**
     * Returns $genderIDArr.
     *
     * @see SavCo_Constants::$genderIDArr
     */
    public function getGenderIDNameArr() {
    	$genderIDNameArr=array('Not Set'=>'gn0','Male'=>'gn1','Female'=>'gn2');
        return $genderIDNameArr;
    }
	/*		
    /**
     //* Returns $sponttedProfileFields.
     // @see SavCo_Constants::$sponttedProfileFields
     
    public function getSponttedProfileFieldNameIDArr() {
    	if (!$sponttedProfileFieldNameIDArr){
			$sponttedProfileFieldNameIDArr=SavCo_ConstantArr::setSponttedProfileFieldNameIDArr();
		} 
        return $sponttedProfileFieldNameIDArr;
    }
    
    /**
     * Sets $sponttedProfileFields.
     *
     * @param object $sponttedProfileFields
     * @see SavCo_Constants::$sponttedProfileFields
     
    public function setSponttedProfileFieldNameIDArr() {
    	$db = Zend_Registry::get(SavCo_ConstantArr::getDbase());//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumsp_sponttedProfileFields'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$sponttedProfileFieldNameArr=array();
		$sponttedProfileFieldIDArr=array();
		
		foreach ($rowset as $row){ 
			array_push($sponttedProfileFieldNameArr,$row['profileField_name']);
			array_push($sponttedProfileFieldIDArr,'sp'.$row['profileField_id']); 
		}
		$sponttedProfileFieldNameIDArr=array_combine($sponttedProfileFieldNameArr,$sponttedProfileFieldIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $sponttedProfileFieldNameIDArr;
    }
    
    
   
     /**
     * Returns $tagSubCategoriesIDNameArr.
     *
     * @see SavCo_Constants::$tagSubCategoriesIDNameArr
     
    public function getTagSubCategoriesIDNameArr() {
    	if (!$tagSubCategoriesIDNameArr){
			$tagSubCategoriesIDNameArr=SavCo_ConstantArr::setTagSubCategoriesIDNameArr();
		} 
        return $tagSubCategoriesIDNameArr;
   }
    
    /**
     * Sets $tagSubCategoriesIDNameArr.
     *
     * @param object $tagSubCategoriesIDNameArr
     * @see SavCo_Constants::$tagSubCategoriesIDNameArr
     
    public function setTagSubCategoriesIDNameArr() {
    	$db = Zend_Registry::get("db");
   		$select = new Zend_Db_Select($db);
		$select->from('enumts_tagSubCategories'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$tagSubCategoriesIDArr=array();
		$tagSubCategoriesNameArr=array();
		
		foreach ($rowset as $row){ 
			array_push($tagSubCategoriesIDArr,'tc'.$row['ID']); 
			array_push($tagSubCategoriesNameArr,$row['name']);
		}
		$tagSubCategoriesIDNameArr=array_combine($tagSubCategoriesIDArr,$tagSubCategoriesNameArr);
		//$cache->save($this->stateIDNameArr);
    	return $tagSubCategoriesIDNameArr;
    }
  
  	    /**
     * Returns $tagIDNameArr.
     *
     * @see SavCo_Constants::$tagIDNameArr
     
    public function getTagIDNameArr() {
     	if (!$tagSubCategoriesIDNameArr){
			$tagIDNameArr=SavCo_ConstantArr::setTagIDNameArr();
		} 
        return $tagIDNameArr;
  }
    
    /**
     * Sets $tagIDNameArr.
     *
     * @param object $tagIDNameArr
     * @see SavCo_Constants::$tagIDNameArr
     
    public function setTagIDNameArr() {
    	$db = Zend_Registry::get("db");
   		$select = new Zend_Db_Select($db);
		$select->from('enumt_tags'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$tagIDArr=array();
		$tagNameArr=array();
		
		foreach ($rowset as $row){ 
			array_push($tagIDArr,'tc'.$row['tag_id']); 
			array_push($tagNameArr,$row['tag_name']);
		}
		$tagIDNameArr=array_combine($tagIDArr,$tagNameArr);
		//$cache->save($this->stateIDNameArr);
    	return $tagIDNameArr;
    }
    */
	 /**
     * Returns $productItemNameIDArr.
     *
     * @see SavCo_Constants::$productItemNameIDArr
     */
    public function getProductItemNameIDArr() {
        $productItemNameIDArr='';
		if (!$productItemNameIDArr){
			$userTypeNameIDArr=SavCo_ConstantArr::setProductItemNameIDArr();
		} 		
        return $productItemNameIDArr;
    }
    
    /**
     * Sets $productItemNameIDArr.
     *
     * @param object $productItemNameIDArr
     * @see SavCo_Constants::$productItemNameIDArr
     */
    public function setProductItemNameIDArr(){
    	$db =SavCo_ConstantArr::getDbase();
   		$select = new Zend_Db_Select($db);
		$select->from("actorsno_ADMIN.enumps_productItemStatus"); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$psIDArr=array();
		$psNameArr=array();
		foreach ($rowset as $row){ 
					array_push($psIDArr,'ps'.$row['productItemStatus_id']); 
					array_push($psNameArr,$row['productItemStatus_name']);
		}	
		$productItemStatusNameIDArr=array_combine($psNameArr,$psIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $productItemStatusNameIDArr;
	}
    
	
	
	
    /**
     * Returns $userTypeNameIDArr.
     *
     * @see SavCo_Constants::$userTypeNameIDArr
     */
    public function getUserTypeNameIDArr() {
		//$cache = Zend_Cache::factory('Core', 'Memcached', $frontendOpts, $backendOpts);
        $userTypeNameIDArr='';
		if (!$userTypeNameIDArr){
			$userTypeNameIDArr=SavCo_ConstantArr::setUserTypeNameIDArr();
		} 
		//Check here for error
		
        return $userTypeNameIDArr;
    }
    
    /**
     * Sets $userTypeNameIDArr.
     *
     * @param object $userTypeNameIDArr
     * @see SavCo_Constants::$userTypeNameIDArr
     */
    public function setUserTypeNameIDArr(){
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from("enumut_usertype");
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$utIDArr=array();
		$utNameArr=array();
		foreach ($rowset as $row){ 
					array_push($utIDArr,'ut'.$row['userType_id']); 
					array_push($utNameArr,$row['userType_name']);
		}	
		$userTypeNameIDArr=array_combine($utNameArr,$utIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $userTypeNameIDArr;
	}
    
    /**
     * Returns $userProfileFieldIDNameArr.
     *
     * @see SavCo_Constants::$userProfileFields
     */
    public static function getUserProfileFieldNameIDArr() {
    	$userProfileFieldNameIDArr='';
    	if (!$userProfileFieldNameIDArr){
			$userProfileFieldNameIDArr=SavCo_ConstantArr::setUserProfileFieldNameIDArr();
		} 

        return $userProfileFieldNameIDArr;
    }
    
    /**
     * Sets $userProfileFieldIDNameArr.
     *
     * @param object $userProfileFields
     * @see SavCo_Constants::$userProfileFields
     */
    public static function setUserProfileFieldNameIDArr() {
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumup_userprofilefields'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$userProfileFieldIDArr=array();
		$userProfileFieldNameArr=array();
		
		foreach ($rowset as $row){ 
					array_push($userProfileFieldIDArr,'up'.$row['profileField_id']); 
					array_push($userProfileFieldNameArr,$row['profileField_name']);
		}
		$userProfileFieldNameIDArr=array_combine($userProfileFieldNameArr,$userProfileFieldIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $userProfileFieldNameIDArr;	
	}   
	
	   /**
     * Returns $sponttProfileFieldIDNameArr.
     *
     * @see SavCo_Constants::$sponttProfileFields
     */
     public static function getSponttProfileFieldNameIDArr() {
     	$sponttProfileFieldNameIDArr=null;
    	if (!$sponttProfileFieldNameIDArr){
			$sponttProfileFieldNameIDArr=SavCo_ConstantArr::setSponttProfileFieldNameIDArr();
		} 

        return $sponttProfileFieldNameIDArr;}
    
    /**
     * Sets $sponttProfileFieldIDNameArr.
     *
     * @param object $sponttProfileFields
     * @see SavCo_Constants::$sponttProfileFields
     */
    public static function setSponttProfileFieldNameIDArr() {
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumsp_sponttProfileFields'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$sponttProfileFieldIDArr=array();
		$sponttProfileFieldNameArr=array();
		
		foreach ($rowset as $row){ 
					array_push($sponttProfileFieldIDArr,'sp'.$row['profileField_id']); 
					array_push($sponttProfileFieldNameArr,$row['profileField_name']);
		}
		$sponttProfileFieldNameIDArr=array_combine($sponttProfileFieldNameArr,$sponttProfileFieldIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $sponttProfileFieldNameIDArr;	
	}   
	
    /**
     * Returns $privacyNameIDSettingArr.
     *
     * @see SavCo_ConstantArr::PrivacyNameIDSettingArr
     */
    public function getPrivacySettingNameIDArr() {
    	$privacySettingIDNameArr=null;
    	if (!$privacySettingIDNameArr){
			$privacySettingNameIDArr=SavCo_ConstantArr::setPrivacyNameIDSettingArr();
		} 

        return $privacySettingNameIDArr;
	}
    
    /**
     * Sets $privacyNameIDSettingArr.
     *
     * @param object $PrivacyNameIDSettingArr
     * @see SavCo_ConstantArr::PrivacyNameIDSettingArr
     */
    private function setPrivacyNameIDSettingArr(){
     	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumps_privacySetting'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$privacySettingNameArr=array();
		$privacySettingIDArr=array();
		
		foreach ($rowset as $row){ 
					array_push($privacySettingIDArr,'ps'.$row['privacySetting_id']); 
					array_push($privacySettingNameArr,$row['privacySetting_name']);
		}
		$privacySettingNameIDArr=array_combine($privacySettingNameArr,$privacySettingIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $privacySettingNameIDArr;
    }

	/**
     * Returns $addressFieldNameIDArr.
     *
     * @see SavCo_ConstantArr::addressFieldNameIDArr
     */
    public function getAddressFieldNameIDArr() {
        $addressFieldNameIDArr='';
    	if (!$addressFieldNameIDArr){
			$addressFieldNameIDArr=SavCo_ConstantArr::setAddressFieldNameIDArr();
		} 
        return $addressFieldNameIDArr;
	}
    
    /**
     * Sets $addressFieldNameIDArr.
     *
     * @param object $addressFieldNameIDArr
     * @see SavCo_ConstantArr::addressFieldNameIDArr
     */
    private function setAddressFieldNameIDArr(){
     	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumaf_addressField'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$addressFieldNameArr=array();
		$addressFieldIDArr=array();
		
		foreach ($rowset as $row){ 
					array_push($addressFieldIDArr,'af'.$row['addressField_id']); 
					array_push($addressFieldNameArr,$row['addressField_name']);
		}
		$addressFieldNameIDArr=array_combine($addressFieldNameArr,$addressFieldIDArr);
	
		//$cache->save($this->stateIDNameArr);
    	return $addressFieldNameIDArr;
    }

		/**
     * Returns $addressTypeNameIDArr.
     *
     * @see SavCo_ConstantArr::addressTypeNameIDArr
     */
    public function getaddressTypeNameIDArr() {
        $addressTypeNameIDArr='';
    	if (!$addressTypeNameIDArr){
			$addressTypeNameIDArr=SavCo_ConstantArr::setAddressTypeNameIDArr();
		} 
        return $addressTypeNameIDArr;
	}
    
    /**
     * Sets $addressTypeNameIDArr.
     *
     * @param object $addressTypeNameIDArr
     * @see SavCo_ConstantArr::addressTypeNameIDArr
     */
    private function setAddressTypeNameIDArr(){
     	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumat_addressType'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$addressTypeNameArr=array();
		$addressTypeIDArr=array();
		
		foreach ($rowset as $row){ 
					array_push($addressTypeIDArr,'at'.$row['addressType_id']); 
					array_push($addressTypeNameArr,$row['addressType_name']);
		}
		$addressTypeNameIDArr=array_combine($addressTypeNameArr,$addressTypeIDArr);
	
		//$cache->save($this->stateIDNameArr);
    	return $addressTypeNameIDArr;
    }

	/**
     * Returns $statusNameIDArr.
     *
     * @see SavCo_ConstantArr::StatusNameIDArr
     */
    public function getStatusNameIDArr() {
        $statusNameIDArr='';
    	if (!$statusNameIDArr){
			$statusNameIDArr=SavCo_ConstantArr::setStatusNameIDArr();
		} 
        return $statusNameIDArr;
	}
    
    /**
     * Sets $statusNameIDArr.
     *
     * @param object $StatusNameIDArr
     * @see SavCo_ConstantArr::StatusNameIDArr
     */
    private function setStatusNameIDArr(){
     	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from('enumst_status'); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$statusNameArr=array();
		$statusIDArr=array();
		
		foreach ($rowset as $row){ 
					array_push($statusIDArr,'st'.$row['status_id']); 
					array_push($statusNameArr,$row['status_name']);
		}
		$statusNameIDArr=array_combine($statusNameArr,$statusIDArr);
	
		//$cache->save($this->stateIDNameArr);
    	return $statusNameIDArr;
    }
	
	
	
	//Admin
	    /**
     * Returns $stateIDNameArr.
     *
     * @see SavCo_Constants::$stateIDNameArr
     */
    static public function getStateAbbNameArr() {
    	//use key to get the state. if not available then sets state.
        $stateAbbNameArr='';
		if (!$stateAbbNameArr){
			$stateAbbNameArr=SavCo_ConstantArr::setStateAbbNameArr();
		} 
		//Check here for error
		
        return $stateAbbNameArr;
    }
    
    /**
     * Sets $stateIDNameArr.
     *
     * @param object $stateIDNameArr
     * @see SavCo_Constants::$stateIDNameArr
     */
    private function setStateAbbNameArr(){
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from("_states"); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$stateAbbArr=array();
		$stateNameArr=array();
		foreach ($rowset as $row){ 
					array_push($stateAbbArr,$row['state_abb']); 
					array_push($stateNameArr,$row['state_name']);
		}
		$stateAbbNameArr=array_combine($stateAbbArr,$stateNameArr);
		//$cache->save($this->stateIDNameArr);
    	return $stateAbbNameArr;
	}
	
	//--------------
	static public function getCityIDStateIDArr() {
    	//use key to get the state. if not available then sets state.
        $cityIDStateIDArr='';

		if (!$cityIDStateIDArr){
			$cityIDStateIDArr=SavCo_ConstantArr::setCityIDStateIDArr();
		} 
		//Check here for error
		
        return $cityIDStateIDArr;
    }
    
    /**
     * Sets $cityIDStateIDArr.
     *
     * @param object $cityIDStateIDArr
     * @see SavCo_Constants::$cityIDStateIDArr
     */
    private function setCityIDStateIDArr(){
    	$db =SavCo_ConstantArr::getDbase();
   		$select = new Zend_Db_Select($db);
		$select->from("_cityIDStateID"); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$cityIDArr=array();
		$stateIDArr=array();
		foreach ($rowset as $row){ 
					array_push($cityIDArr,$row['city_id']); 
					array_push($stateIDArr,$row['state_id']);
		}
		$cityIDStateIDArr=array_combine($cityIDArr,$stateIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $cityIDStateIDArr;
	}
	
	
	
	
	
	
	/**
     * Returns $appNameIDArr.
     *
     * @see SavCo_Constants::$appNameIDArr
     */
    static public function getAppNameIDArr() {
    	//use key to get the state. if not available then sets state.
        $appNameIDArr='';
		if (!$appNameIDArr){
			$appNameIDArr=SavCo_ConstantArr::setAppNameIDArr();
		} 
		//Check here for error
		
        return $appNameIDArr;
    }
    
    /**
     * Sets $appNameIDArr.
     *
     * @param object $appNameIDArr
     * @see SavCo_Constants::$appNameIDArr
     */
    private function setAppNameIDArr(){
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from("actorsno_ADMIN.app"); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$appIDArr=array();
		$appNameArr=array();
		foreach ($rowset as $row){ 
					array_push($appIDArr,'ap'.$row['ID']); 
					array_push($appNameArr,$row['name']);
		}
		$appNameIDArr=array_combine($appNameArr,$appIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $appNameIDArr;
	}

	/**
     * Returns $carrierNameEmailExtArr.
     *
     * @see SavCo_Constants::$carrierNameEmailExtArr
     */
    static public function getCarrierNameEmailExtArr() {
    	//use key to get the state. if not available then sets state.
        $carrierNameEmailExtArr='';
		if (!$carrierNameEmailExtArr){
			$carrierNameEmailExtArr=SavCo_ConstantArr::setCarrierNameEmailExtArr();
		} 
		//Check here for error
		
        return $carrierNameEmailExtArr;
    }
    
    /**
     * Sets $carrierNameEmailExtArr.
     *
     * @param object $carrierNameEmailExtArr
     * @see SavCo_Constants::$carrierNameEmailExtArr
     */
    private function setCarrierNameEmailExtArr(){
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from("carrier"); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$carrierNameArr=array();
		$carrierEmailExtArr=array();
		foreach ($rowset as $row){ 
					array_push($carrierNameArr,$row['name']); 
					array_push($carrierEmailExtArr,$row['emailextension']);
		}
		$carrierNameEmailExtArr=array_combine($carrierNameArr,$carrierEmailExtArr);
		//$cache->save($this->stateIDNameArr);
    	return $carrierNameEmailExtArr;
	}
	
	/**
     * Returns $phoneTypeNameIDArr.
     *
     * @see SavCo_Constants::$phoneTypeNameIDArr
     */
    static public function getPhoneTypeNameIDArr() {
    	//use key to get the state. if not available then sets state.
        $phoneTypeNameIDArr='';
		if (!$phoneTypeNameIDArr){
			$phoneTypeNameIDArr=SavCo_ConstantArr::setPhoneTypeNameIDArr();
		} 
		//Check here for error
		
        return $phoneTypeNameIDArr;
    }
    
    /**
     * Sets $phoneTypeNameIDArr.
     *
     * @param object $phoneTypeNameIDArr
     * @see SavCo_Constants::$phoneTypeNameIDArr
     */
    private function setPhoneTypeNameIDArr(){
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from("phoneType"); 
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$phoneTypeNameArr=array();
		$phoneTypeIDArr=array();
		foreach ($rowset as $row){ 
					array_push($phoneTypeIDArr,'pt'.$row['ID']); 
					array_push($phoneTypeNameArr,$row['name']);
		}
		$phoneTypeNameIDArr=array_combine($phoneTypeNameArr,$phoneTypeIDArr);
		//$cache->save($this->stateIDNameArr);
    	return $phoneTypeNameIDArr;
	}	
  
 	/**
     * Returns $bugTypeNameIDArr.
     *
     * @see SavCo_Constants::$bugTypeNameIDArr
     */
    static public function getBugTypeNameIDArr() {
    	//use key to get the state. if not available then sets state.
        $bugTypeNameIDArr='';
		if (!$bugTypeNameIDArr){
			$bugTypeNameIDArr=SavCo_ConstantArr::setBugTypeNameIDArr();
		} 
		//Check here for error
		
        return $bugTypeNameIDArr;
    }
    
    
	/**
     * Returns $bannedWordsArr.
     *
     * @see SavCo_Constants::$bannedWordsArr
     */
    static public function getBannedWordsArr() {
    	//use key to get the state. if not available then sets state.
		$bannedWordsArr=null;
		if (!$bannedWordsArr){
			$bannedWordsArr=SavCo_ConstantArr::setBannedWordsArr();
		} 
		//Check here for error
		
        return $bannedWordsArr;
    }
    
    /**
     * Sets $bannedWordsArr.
     *
     * @param object $bannedWordsArr
     * @see SavCo_Constants::$bannedWordsArr
     */
    private function setBannedWordsArr(){
    	$db = Zend_Registry::get('db');//$this->sponttDB);
   		$select = new Zend_Db_Select($db);
		$select->from("_cache_badWords");
    	$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
	
		$bannedWordsArr=array();
		$bannedWordsArr=array();
		foreach ($rowset as $row){ 
					array_push($bannedWordsArr,$row['word']); 
		}
    	return $bannedWordsArr;
	}
	
	
	
	
    /**
     * Returns $dbase.
     *
     * @see SavCo_ConstantArr::$dbase
     */
    static public function getDbase() {
    	$db = Zend_Registry::get('db');//$this->sponttDB);
        return $db;
    }
	

	
	/**
     * Returns $logEvent.
     *
     * @see SavCo_ConstantArr::$logEvent
     */
    static public function getLogEvent() {
		$logger= Zend_Registry::get('logEvent');
		return $logger;
    }
	/**
     * Returns $logEverytime.
     *
     * @see SavCo_ConstantArr::$logEverytime
     */
    static public function getLogEverytime() {
		$logger= Zend_Registry::get('logEverytime');
		return $logger;
    }
 }