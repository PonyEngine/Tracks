<?
    class SavCoLogger extends Zend_Log_Writer_Stream 
    {
        public function __construct($streamOrUrl, $mode = 'a')
        {		
			parent::__construct($streamOrUrl, $mode = 'a');
		}
		
     	/*protected function _write($event)
    	{
    		$detail=1;
			//SAVCO - Make more explicit
    		//Add time and location
			//$event=time().$event;
			//$event=$event."test";
			//print_r($event);
			//Override Timestamp
			//print_r($event);
			//Get the identity and Role
			$auth = Zend_Auth::getInstance();
			$actIdent=$auth->getIdentity();
			$userType=strlen($actIdent->user_type)>0?$actIdent->user_type:'guest';
			//$event[timestamp]=time().':';
			
			$browser=':';
			if (isset($registry->requestingDevice)) {
				$browser=$registry->requestingDevice->getCapability("brand_name").':';
			}
			
			
			$event[message]=$_SERVER['REMOTE_ADDR'].':'.$browser.'m'.$actIdent->user_id.':'.$userType.':'.$event[message];
			//$event[priority]=time();
			//$event[priorityName]=time();
		
			parent::_write($event);		
    	}*/
 }
