<?php
    class FormProcessor_UserLocation extends FormProcessor
    {
        protected $user;
        public $location;

        public function __construct(DatabaseObject_User $user)
        {
            parent::__construct();

            $this->user = $user;

            // set up the initial values for the new location
            $this->location = new DatabaseObject_UserLocation($user->getDb());
            $this->location->user_id = $this->user->getId();
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            $this->description = $this->sanitize($request->getPost('description'));
			
			$this->address1 = $this->sanitize($request->getPost('address1'));
			
			if(strlen(trim($this->address1))==0){
				$this->addError('address1', 'You must enter a valid address.');
			}
			
			$this->address2 = $this->sanitize($request->getPost('address2'));
			
			$this->city_cityState = $this->sanitize($request->getPost('city_cityState'));
			$cityState=explode(',',$this->city_cityState);
			$index=0;
			foreach($cityState as $part){
				if(!trim($part)){
					SavCo_ConstantArr::getLogEvent()->warn('No data for citystate in position '.$index,1);
				}
				$index++;
			}
			
			$this->city=$cityState[0];
			$this->state=$cityState[1];
			$this->city_id=SavCo_FunctionsDB::getCityIDFromCityStateorStateAbb(array('city'=>$this->city,'state'=>$this->state));
			
			if ($this->city_id<1){
            		$this->addError('city_cityState', 'We can not locate this city and state.');
			}
			
			//GET THE REMAINING BASIC INFORMATION
            if (!$this->hasError()) {
	 			$geo=SavCo_Geocoder::getLatLonZip($this->address1,$this->city,$this->state);
				$this->user_lat=$geo['lat'];
				$this->user_lon=$geo['lon'];
              }

   
            // if no errors have occurred, save the location
            if (!$this->hasError()) {
                $this->location->description = $this->description;
			
               	$addressJSON['address1'] = $this->address1;
				$addressJSON['address2'] = $this->address2;
				$addressJSON['city'] = $this->city;
				$addressJSON['state'] = $this->state;
				$this->location->city_id= $this->city_id;
			    $this->location->user_lat= $this->user_lat;
                $this->location->user_lon = $this->user_lon;
				$this->location->addressJSON=Zend_Json::encode($addressJSON);
				$this->location->addressType= 'default';
                $this->location->save();
            }

            return !$this->hasError();
        }
    }
?>