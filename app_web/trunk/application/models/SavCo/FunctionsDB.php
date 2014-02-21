<?
    class SavCo_FunctionsDB extends SavCo
    {
    	//I like having this in a seperate database keeps our factual data
		//separate from the application data
		//require_once('/home8/actorsno/public_html/admin/class/contact.class.php');
 		//Set sessions here can add another level by check IP Addresses
 		//Can check latest code can very also one telphone input
		//city
        protected $db = null;
        public $launchemail = null;

        public function __construct($db)
        {
            parent::__construct();
             $this->db = Zend_Registry::get('db');
        }
		
        public static function CleanDB(){
	        $db=SavCo_ConstantArr::getDbase();
	        $db->query("TRUNCATE `companies`");
		    $db->query("TRUNCATE `service_1_reserve`");
		    $db->query("TRUNCATE `service_4_audios`");
		    $db->query("TRUNCATE `service_5_photos`");
		    $db->query("TRUNCATE `service_6_connected`");
		    $db->query("TRUNCATE `sponttedUsers`");
		    $db->query("TRUNCATE `spontts`");
		    $db->query("TRUNCATE `spontts_locations`");
		    $db->query("TRUNCATE `spontts_profile`");
		    $db->query("TRUNCATE `spontts_services`");
		    $db->query("TRUNCATE `spontts_tags`");
		    $db->query("TRUNCATE `spontt_qeue`");
		    $db->query("TRUNCATE `users`");
		    $db->query("TRUNCATE `users_images`");
		    $db->query("TRUNCATE `users_locations`");
		    $db->query("TRUNCATE `users_presences`");
		    $db->query("TRUNCATE `users_profile`");
		    $db->query("TRUNCATE `users_tags`");
		    $db->query("TRUNCATE `_checkLogin`");
        } 
        


		public static function getCityStateSuggestions($partialWord, $limit = 20)
        {	$db=SavCo_ConstantArr::getDbase();
            $partialWord = trim($partialWord);
			//get spontts that are live and actually meet the string entered
			
            if (strlen($partialWord) == 0)
                return array();
//This should handle before the , and after- do switch here
            	$select = 'SELECT city_name, state_abb FROM _cities'; 
    			$select .=' INNER JOIN _cityIDStateID on _cityIDStateID.city_id=_cities.city_id ';
		 		$select .=' INNER JOIN _states on _cityIDStateID.state_id=_states.state_id WHERE ';
				$select .=' city_name LIKE "'.$partialWord.'%"';
				
				$stmt=$db->query($select);
				$rowset=$stmt->fetchAll();
	
				$result=array();
				foreach ($rowset as $row){ 
					$spontt['classType']='cityName';
					$spontt['result']=$row['city_name'].', '.$row['state_abb']; 
					$result[]=$spontt;
				}	
		
           return $result;
        }
	
	    static public function getCityNameStateNameFromCityID($cityID){
				//get the CityID from the name
				$CityNameStateName['city']='';
				$CityNameStateName['state']='';
				
				if (is_integer($cityID)){
					$db = SavCo_ConstantArr::getDbase();
					$select='SELECT _cities.city_name,_states.state_name FROM _cities';
					$select.=' INNER JOIN _cityIDStateID ON  _cities.city_id=_cityIDStateID.city_id ';
					$select.=' INNER JOIN _states ON _cityIDStateID.state_id= _states.state_id';
					$select.=' WHERE _cities.city_id ='.$cityID;
				
					$stmt=$db->query($select);
					$rowset=$stmt->fetchAll();
					
					if(count($rowset)==1){
						foreach ($rowset as $row){ 
							print_r($row);
							$CityNameStateName['city']=$row['city_name'];
							$CityNameStateName['state']=$row['state_name'];
						}		
					}
				}
				return $CityNameStateName;
				
	}

		 static public function getCityNameStateAbbFromCityID($cityID){
				//get the CityID from the name
				$CityNameStateName['city']='';
				$CityNameStateName['state_abb']='';
				
				if (is_numeric($cityID) && $cityID>0){
				
					$db = SavCo_ConstantArr::getDbase();
					$select='SELECT _cities.city_name,_states.state_abb FROM _cities';
					$select.=' INNER JOIN _cityIDStateID ON  _cities.city_id=_cityIDStateID.city_id ';
					$select.=' INNER JOIN _states ON _cityIDStateID.state_id= _states.state_id';
					$select.=' WHERE _cities.city_id ='.$cityID;
				
					$stmt=$db->query($select);
					$rowset=$stmt->fetchAll();
					if(count($rowset)==1){//should be ==1
						foreach ($rowset as $row){ 
							$CityNameStateName['city']=$row['city_name'];
							$CityNameStateName['state_abb']=$row['state_abb'];
						}		
					}else{
						$message=sprintf('Could not get a cityID for IP %s',$_SERVER['REMOTE_ADDR']);
						$logger= Zend_Registry::get('logEvent');
						$logger->error($message,1);
					}
				}
				return $CityNameStateName;
				
	} 
		
		static function getCityIDFromCityNameStateAbb($cityName='',$stateAbb=''){
			//get the CityID from the name
			$cityID=0;
			$cityIDArr=SavCo_FunctionsDB::getCityIDArrfromCityName($cityName);
			
			if(count($cityIDArr)>0){
				$stateID=SavCo_FunctionsDB::getStateIDfromStateorStateAbb($stateAbb);
				
				$db = SavCo_ConstantArr::getDbase();
				
    			$stmt=$db->query('SELECT city_id FROM _cityIDStateID WHERE city_id IN ('.implode(',',$cityIDArr).') AND state_id="'.$stateID.'"');
				$rowset=$stmt->fetchAll();
				
				if(count($rowset)==1){
					foreach ($rowset as $row){ 
						$cityID=$row['city_id'];
					}		
				}
				
			}
			return $cityID;
		}	
		
		static function getLatLonFromFromCityNameStateAbb($cityName='',$stateAbb=''){
			//get the CityID from the name
			$geo['lat']=0;
			$geo['lon']=0;
			
			$geo=SavCo_FunctionsDB::getLatLonfromCityID(SavCo_FunctionsDB::getCityIDFromCityNameStateAbb($cityName,$stateAbb));

			return $geo;
		}	
		
	
	static public function getStateIDfromStateorStateAbb($stateorStateAbb=''){
		$db = SavCo_ConstantArr::getDbase();//$this->sponttDB);
		$stateorStateAbb=trim($stateorStateAbb);
    	$select='SELECT state_id FROM _states WHERE state_abb="'.$stateorStateAbb.'" OR state_name="'.$stateorStateAbb.'"';
		$stmt=$db->query($select);
		$rowset=$stmt->fetchAll();
		$rowcount=count($rowset);
		
		if($rowcount>0){
			if($rowcount>1){
				SavCo_ConstantArr::getLogEvent()->warn('Multiple ids found for '.$stateorStateAbb,1);
			}
			return $rowset[0]['state_id'];
		}else{
			//Log this as an ERROR
			SavCo_ConstantArr::getLogEvent()->warn('No stateid found for '.$stateorStateAbb,1);
			return 0;
		}
		return 0;
	}
	
		
	
	static public function getStateIDfromStateName($stateName=''){
		$db = SavCo_ConstantArr::getDbase();
    	$select="SELECT ID FROM state WHERE name=?";
		$stmt=$db->query($select,array($stateName));
		$rowset=$stmt->fetchAll();
		$rowcount=count($rowset);
		
		if($rowcount==1){
			   return $rowset[0]['ID'];
		}else{
			//Log this as an ERROR
			SavCo_ConstantArr::getLogEvent()->warn('No stateid found for '.$stateName,1);
			return 0;
		}
		return 0;
	}
	static public function getCityIDArrfromCityName($cityName=''){
		$db = SavCo_ConstantArr::getDbase();
    	$select="SELECT city_id FROM _cities WHERE city_name=?";
		$stmt=$db->query($select,array($cityName));
		$rowset=$stmt->fetchAll();
		$rowcount=count($rowset);
		$cityArr=array();
		if($rowcount>0){
			foreach ($rowset as $row){ 
				
				if (array_key_exists('ID',$row))array_push($cityArr,$row['ID']);
			}
			 return $cityArr;
		}else{
			//Log this as an ERROR
			SavCo_ConstantArr::getLogEvent()->warn('No cityid found for '.$cityName,1);
			return $cityArr;
		}
		return $cityArr;
	}
	
	static function getCityIDFromCityStateName($cityName='',$stateName=''){
			$cityName=trim($cityName);
			//get the CityID from the name
			$cityIDArr=SavCo_FunctionsDB::getCityIDArrfromCityName($cityName);
			
			//print "City name is".$cityName;
			//print "State name is".$stateName;
			if(count($cityIDArr)>0){
				$stateID=SavCo_FunctionsDB::getStateIDfromStateName($stateName);
				
				$db = SavCo_ConstantArr::getDbase();
    			$stmt=$db->query('SELECT city_id FROM cityIDStateID WHERE city_id IN ('.implode(',',$cityIDArr).') AND state_id="'.$stateID.'"');
				
    			$rowset=$stmt->fetchAll();
				$rowcount=count($rowset);
				//print "Rowcount is ".$rowcount;
				foreach ($rowset as $row){ 
					return $row['city_id']; 
				}
			}else{
				return false;
			}
		
		}
		static function getCityIDFromCityStateAbb($cityName='',$stateAbb=''){
			$cityName=trim($cityName);
			//get the CityID from the name
			 $cityIDArr=SavCo_FunctionsDB::getCityIDArrfromCityName($cityName);
			
			//print "City name is".$cityName;
			//print "State abb 4 is".$stateAbb;
			
			if(count($cityIDArr)>0){
				 $stateID=SavCo_FunctionsDB::getStateIDfromStateorStateAbb($stateAbb);
				print "State is is".$stateID.' <br />';
				
				$db = SavCo_ConstantArr::getDbase();
    			$stmt=$db->query('SELECT city_id FROM _cityIDStateID WHERE city_id IN ('.implode(',',$cityIDArr).') AND state_id="'.$stateID.'"');
				$rowset=$stmt->fetchAll();
				$rowcount=count($rowset);
				print "Rowcount is ".$rowcount.'<br/>';
				foreach ($rowset as $row){ 
					return  $row['city_id']; 
				}
			}else{
				return false;
			}
		
		}	
		//Main Function Logic Call
		static public function getLatLonFromAddressCityIDArr($addressCityId){
			$geo['lat']=null;
			$geo['lon']=null;
			if(strlen($addressCityId['address'])>0){
				
				$cityNameStateName=SavCo_FunctionsDB::getCityNameStateNameFromCityID($addressCityId['cityID']);
				$geo=SavCo_FunctionsDB::getGeoFromYahooDotCom($addressCityId['address'],$cityNameStateName['city'],$cityNameStateName['state']);
			}
			
			//If no data collected from previous geo 
			if ($geo['lat']==null){
				//echo "Choice2";
				//try to get it from the cityID
				$geo=SavCo_FunctionsDB::getLatLonfromCityID($addressCityId['cityID']);
			}
			return $geo;
		}
		static public function getCityIDFromCityStateorStateAbb($cityStateorStateAbb){
			$cityID=0;
			$cityIDArr=SavCo_FunctionsDB::getCityIDArrfromCityName($cityStateorStateAbb['city']);
			
			if(count($cityIDArr)>0){
				$stateID=SavCo_FunctionsDB::getStateIDfromStateorStateAbb($cityStateorStateAbb['state']);
				
				$db = SavCo_ConstantArr::getDbase();
    			$stmt=$db->query('SELECT city_id FROM _cityIDStateID WHERE city_id IN ('.implode(',',$cityIDArr).') AND state_id="'.$stateID.'"');
    			$rowset=$stmt->fetchAll();
				$rowcount=count($rowset);
				
				if($rowcount> 0){
					if($rowcount>1){
						SavCo_ConstantArr::getLogEvent()->warn('Multiple ids found forcity '.$cityStateorStateAbb,1);
					}
					
					foreach ($rowset as $row){ 
						$cityID=$row['city_id'];
					}		
				}
				
			}
			return $cityID;	
		}
		
		
		static private function getLatLonfromCityID($cityID){	
			$geo['lat']=null;
			$geo['lon']=null;
					
			if($cityID>0){
				$db = SavCo_ConstantArr::getDbase();
    			$stmt=$db->query('SELECT city_lat,city_lon FROM _cities WHERE city_id='.$cityID);
				$rowset=$stmt->fetchAll();
				$rowcount=count($rowset);
				
				if($rowcount==1){
					foreach ($rowset as $row){ 
							$geo['lat']=$row['city_lat'];
							$geo['lon']=$row['city_lon']; 
					}
				}
			}
			return $geo;
		}
	
	static private function getGeoFromYahooDotCom($address='',$cityName='',$stateName=''){
			//http://developer.yahoo.com/maps/rest/V1/geocode.html
			//Limit is 5,000 every 24 hours
			//appid  	string (required)  	The application ID. See Application IDs for more information.
			//street 	string 	Street name. The number is optional.
			//city 	string 	City name.
			//state 	string 	The United States state. You can spell out the full state name or you can use the two-letter abbreviation.
			//zip 	integer or <integer>-<integer> 	The five-digit zip code, or the five-digit code plus four-digit extension. If this location contradicts the city and state specified, the zip code will be used for determining the location and the city and state will be ignored.
			//location 	free text 	
			/*This free field lets users enter any of the following:
    			* city, state
    		* city, state, zip
   				 * zip
    			* street, city, state
    * street, city, state, zip
    * street, zip

If location is specified, it will take priority over the individual fields in determining the location for the query. City, state and zip will be ignored.
output 	string: xml (default), php 	The format for the output. If php is requested, the results will be returned in Serialized PHP format.
		ExMPLE:http://local.yahooapis.com/MapsService/V1/geocode?appid=YD-9G7bey8_JXxQP6rxl.fBFGgCdNjoDMACQA--&street=701+First+Ave&city=Sunnyvale&state=CA
			
			*/
			
			$yahooAppID='.zqItn7V34GJ_WKDgmOWO8dh4hbKOhjl.Ld9OsGlmlDiHoNJUoVfYGzkTQ5aS2sPGsgvLFk-';
			$output='&output=php';
			$urlHead='http://local.yahooapis.com/MapsService/V1/geocode?';
			$urlRequest=$urlHead.'appid='.$yahooAppID.'&street='.urlencode($address).'&city='.urlencode($cityName).'&state='.urlencode($stateName).$output;
			//print $urlRequest.'<br />';
			$serializedArr=SavCo_FunctionsGen::restGETURL($urlRequest);
			$dataArr=unserialize($serializedArr);
			$ResultSetArr=$dataArr['ResultSet'];
			$ResultArr=$ResultSetArr['Result'];
			$geo['lat']=$ResultArr['Latitude'];
			$geo['lon']=$ResultArr['Longitude'];
			$geo['zip']=$ResultArr['Zip'];
			#echo "The here zip is".$this->zip;
			return $geo;
		}
		
	static public function getGeoPluginArrFromIP($ip)
 	{ 
 		$geoPluginURL = "http://www.geoplugin.net/php.gp?ip=".$ip; 
 		$geoArr=SavCo_FunctionsGen::restGETURL($geoPluginURL);
		/*		array (
  				'geoplugin_city' => 'Los Angeles',
  				'geoplugin_region' => 'CA',
  				'geoplugin_areaCode' => '310',
  				'geoplugin_dmaCode' => '803',
  				'geoplugin_countryCode' => 'US',
  				'geoplugin_countryName' => 'United States',
  				'geoplugin_continentCode' => 'NA',
  				'geoplugin_latitude' => '34.065701',
  				'geoplugin_longitude' => '-118.436203',
  				'geoplugin_currencyCode' => 'USD',
  				'geoplugin_currencySymbol' => '$',
  				'geoplugin_currencyConverter' => 1,
			)*/
		
 		return unserialize($geoArr);
 	}
	
	static public function getLatLon(){
		$geo=SavCo_FunctionsDB::getLatLonFromIP();
		return $geo;
	}	

	static public function getLatLonFromIP(){
		$geoData=SavCo_FunctionsDB::getGeoPluginArrFromIP($_SERVER['REMOTE_ADDR']);
		$geo['lat']=$geoData['geoplugin_latitude'];
		$geo['lon']=$geoData['geoplugin_longitude'];
		
		return $geo;
	}	
	
	
	static public function getCoordsFromZipCode($zip){
			$db = SavCo_ConstantArr::getDbase();
			$select='SELECT zipcode_lat,zipcode_lon FROM _zipcodes';
			$select.=' WHERE _zipcodes.zip='.$zip;
			$coords=array('lat'=>'','lon'=>'');
			
			$stmt=$db->query($select);
			$rowset=$stmt->fetchAll();
					
			if(count($rowset)==1){
				foreach ($rowset as $row){ 
					$coords['lat']=$row['zipcode_lat'];
					$coords['lon']=$row['zipcode_lon'];		
				}
			}
		return $coords;
	}
		
	
	function getLatLonCityStateRegionNameArrayfromCurrentIP($currentIP){
			  	$latLonArr=-1;
				
				if (strlen(trim($currentIP))>0){
					$conn=openMySql();
					$query='SELECT cityID,lat,lon FROM checkLogin WHERE ipAddress="'.$currentIP.'" ORDER BY `checkLogin`.`ID` DESC ';
					$result=mysql_query($query) or die("Error at $query"); 

					$latLonCityStateRegionArrName=array();
					$latLonCityStateRegionArrLoc=array();

					//Kind of Hacky- Find First Occurence - May write unique table in the future
					//Also a test inprocessing large tables
					$rowCount=mysql_num_rows($result);
					
					if ($rowCount >0){
						$line = mysql_fetch_array($result); 
						array_push($latLonCityStateRegionArrName,'lat');
						array_push($latLonCityStateRegionArrLoc,escapeString($line['lat']));
						array_push($latLonCityStateRegionArrName,'lon'); 
						array_push($latLonCityStateRegionArrLoc,escapeString($line['lon']));
						
						array_push($latLonCityStateRegionArrName,'cityState');
						$cityID=escapeString($line['cityID']); 
						
						if (is_numeric($cityID)){
							
							$cityStateIDArr=getCityStateIDArrayfromCityID($cityID);
							array_push($latLonCityStateRegionArrLoc,escapeString($cityStateIDArr['cityName'].','.getStateAbbfromStateID($cityStateIDArr['stateID'])));
						}else{
							array_push($latLonCityStateRegionArrLoc,$cityID);//Give the full name with Comma
						}	
					}
					#print_r($latLonCityStateRegionArrName);
					#print_r($latLonCityStateRegionArrLoc);
					$latLonCityStateRegionArr= array_combine($latLonCityStateRegionArrName, $latLonCityStateRegionArrLoc);
					} 
					
				return $latLonCityStateRegionArr;
	}



	function getCarrierNamefromID($carrierID){
		$carrierName='';
	
		$conn=openMySql();
		$query='SELECT name FROM carrier WHERE ID="'.$carrierID.'" LIMIT 1';   //Presently gets the last one
		
		$result=mysql_query($query) or die("Error at $query"); 
		while ($line = mysql_fetch_array($result))   { 
			$carrierName=escapeString($line['name']);		
		}
		return $carrierName;
	}
						
}
