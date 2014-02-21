<?
    class SavCo_ValidateDB extends SavCo
    {
    	//I like having this in a seperate database keeps our factual data
		//separate from the application data
		//require_once('/home8/actorsno/public_html/admin/class/contact.class.php');
 		//Set sessions here can add another level by check IP Addresses
 		//Can check latest code can very also one telphone input
		//city
        static public $db='db2';
		public $launchemail = null;

        public function __construct($db)
        {
            parent::__construct();
            $this->db = Zend_Registry::get('db2');
        }
	
		static function validateCityStateAbb($cityName='',$stateAbb=''){
			//get the CityID from the name
			
			$cityIDArr=SavCo_FunctionsDB::getCityIDArrfromCityName($cityName);
			if(count($cityIDArr)>0){
				$stateID=SavCo_FunctionsDB::getStateIDfromStateAbb($stateAbb);
				
				$db2 = SavCo_ConstantArr::getDbase2();
    			$stmt=$db2->query('SELECT city_id FROM actorsno_ADMIN._cityIDStateID WHERE city_id IN ('.implode(',',$cityIDArr).') AND state_id="'.$stateID.'"');
				$rowset=$stmt->fetchAll();
				if(count($rowset)==1){
					return true;	
				}else{
					return false;
				}
				
			}else{
				return false;
			}
			
		}
	
			
	}
