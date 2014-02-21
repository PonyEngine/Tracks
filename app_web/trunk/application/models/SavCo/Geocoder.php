<?
	class SavCo_Geocoder extends SavCo{
		private $streetAddress='';
		private $city='';
		private $state='';
		private $zip='';
		private $lat='';
		private $lon='';
		function __construct(){		
	}
				
	static public function getLatLonZip($address,$cityName,$stateName){	
		//This is so We can switch services ti get the lat andlong
			$geoData=SavCo_Geocoder::callYahooApi(urlencode($address),urlencode($cityName),urlencode($stateName));
			return 	$geoData;		
		}
		static public function GetLatLonZipFromIP(){
			//$_SERVER["REMOTE_ADDR"];
		}
		
		
		static public function GetAddressInfo($spontt_lat,$spontt_lon,$sensorBool='false'){
				//http://groups.google.com/group/Google-Maps-API/web/resources-non-google-geocoders
				//Found-http://code.google.com/apis/maps/documentation/geocoding/
				$urlRequest='http://maps.googleapis.com/maps/api/geocode/json?latlng='.$spontt_lat.','.$spontt_lon.'&sensor=false';				
		       
				 $jsonArr=SavCo_FunctionsGen::restGETURL($urlRequest);
				//echo $urlRequest;
				
				//print_r($jsonArr);
				$data=Zend_Json::decode($jsonArr);
				
				/*print $data['results'][0]['address_components'][0]['long_name']; //Number
				print '<br/>';
				print $data['results'][0]['address_components'][1]['long_name']; //Route
				print '<br/>';
				//print $data['results'][0]['address_components'][2]['long_name']; //Locality
				print '<br/>';
				print $data['results'][0]['address_components'][3]['long_name']; //administrative_area_level_3/City
				print '<br/>';
				//print $data['results'][0]['address_components'][4]['long_name']; //administrative_area_level_2
				print '<br/>';
				print $data['results'][0]['address_components'][5]['long_name']; //administrative_area_level_1/State
				print '<br/>';
				print $data['results'][0]['address_components'][6]['long_name']; //Country	*/
			    
				
				foreach( $data['results'][0]['address_components'] as $component){
					switch($component['types'][0]){
						case 'street_number':
							$streetNumber=$component['long_name']; //street number
						break;
						case 'route':
							$route=$component['long_name']; //route
						break;
						case 'locality':
							$locality[]=$component['long_name']; //area
						break;
						case 'administrative_area_level_1': //state	
							$state=$component['long_name'];
						break;
						case 'administrative_area_level_2': //county?	
							$county=$component['long_name'];
						break;		
						case 'administrative_area_level_3': //city	
							$city=$component['long_name'];
						break;
						case 'country': //state	
							$country=$component['long_name'];
						break;
						case 'postal_code': //state	
							$zipCode=$component['long_name'];
						break;
					}
					//print $component['long_name'].'<br />';
				};

				$address['address1'] = $streetNumber.' '.$route;
				//$address['locality'] =$locality;
				$address['county'] =$county;
				$address['city'] = $city;
				$address['state'] = $state;
				$address['country'] = $country;	
				//print_r($address);		
			return $address;	
		}
		
		static private function callYahooApi($address,$cityName,$stateName){
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
			$urlRequest=$urlHead.'appid='.$yahooAppID.'&street='.$address.'&city='.$cityName.'&state='.$stateName.$output;
			$serializedArr=SavCo_FunctionsGen::restGETURL($urlRequest);
			
			///print $serializedArr;
			$dataArr=unserialize($serializedArr);
			
			//print_r ($dataArr);
			
			$ResultSetArr=$dataArr['ResultSet'];
			$ResultArr=$ResultSetArr['Result'];
			$geo['lat']=$ResultArr['Latitude'];
			$geo['lon']=$ResultArr['Longitude'];
			$geo['zip']=$ResultArr['Zip'];
			#echo "The here zip is".$this->zip;
			return $geo;
		}
		
		

    
    /**
     * Returns $city.
     *
     * @see gecoder::$city
     */
    public function getCity() {
        return $this->city;
    }
    
    /**
     * Sets $city.
     *
     * @param object $city
     * @see gecoder::$city
     */
    public function setCity($city) {
        $this->city = urlencode($city);
    }
    
    /**
     * Returns $state.
     *
     * @see gecoder::$state
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Sets $state.
     *
     * @param object $state
     * @see gecoder::$state
     */
    public function setState($state) {
        $this->state = urlencode($state);
    }
    
    /**
     * Returns $streetAddress.
     *
     * @see gecoder::$streetAddress
     */
    public function getStreetAddress() {
        return $this->streetAddress;
    }
    
    /**
     * Sets $streetAddress.
     *
     * @param object $streetAddress
     * @see gecoder::$streetAddress
     */
    public function setStreetAddress($streetAddress) {
        $this->streetAddress = urlencode($streetAddress);
    }
    
    
    /**
     * Returns $yahooAppID.
     *
     * @see gecoder::$yahooAppID
     */
    public function getYahooAppID() {
        return $this->yahooAppID;
    }
    
    /**
     * Returns $zip.
     *
     * @see gecoder::$zip
     */
    public function getZip() {
        return $this->zip;
    }
    
    /**
     * Sets $zip.
     *
     * @param object $zip
     * @see gecoder::$zip
     */
    public function setZip($zip) {
        $this->zip = urlencode($zip);
    }
    
    
    /**
     * Returns $lat.
     *
     * @see geocoder::$lat
     */
    public function getLat() {
        return $this->lat;
    }
    
    /**
     * Returns $lon.
     *
     * @see geocoder::$lon
     */
    public function getLon() {
        return $this->lon;
    }
   }