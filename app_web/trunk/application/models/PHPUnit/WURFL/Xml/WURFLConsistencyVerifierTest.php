<?php

class WURFL_Xml_WURFLConsistencyVerifierTest extends PHPUnit_Framework_TestCase {
	
	private $devices;
	
	protected function setUp() {		
		parent::setUp();
		$groupIdCapabilitiesMap["product_info"]["model_name"] = "value";
		$genericDevice = new WURFL_Xml_ModelDevice ( "generic", "", "", TRUE, $groupIdCapabilitiesMap );
		
		$someGroupIdCapabilitiesMap["x"]["y"] = "z"; 		
		$someDevice = new WURFL_Xml_ModelDevice ( "nokia", "nokia", "", true, $someGroupIdCapabilitiesMap );
		
		$devices [$genericDevice->id] = $genericDevice;
		$devices [$someDevice->id] = $someDevice;
				
		$this->devices = $devices;
	}
	
	
	/**
	 * @expectedException WURFL_WURFLException
	 *
	 */
	public function testShoudThrowWURFLExceptionWhenDefiningANewGroupNotPresentInGenericDevice() {		
		WURFL_Xml_WURFLConsistencyVerifier::verify($this->devices);
	}

	/**
	 * @expectedException WURFL_WURFLException
	 *
	 */
	public function testShoudThrowWURFLExceptionWhenDefiningACapabilityNotPresentInGenericDevice() {
		$groupIdCapabilitiesMap["product_info"]["new_capbility"] = "new_capability"; 		
		$device = new WURFL_Xml_ModelDevice ( "nokia", "nokia", "", true, $groupIdCapabilitiesMap );
		$this->devices[$device->id] = $device;
		
		WURFL_Xml_WURFLConsistencyVerifier::verify($this->devices);
	}
	
	
	
	
}

?>