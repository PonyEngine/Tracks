<?php

require_once 'PHPUnit/Framework.php';

require_once dirname ( __FILE__ ) . '/../../../WURFL/WURFLManagerProvider.php';

require_once 'TestUtils.php';

class WURFL_WURFLManagerTest extends PHPUnit_Framework_TestCase {
	
	protected $wurflManager;
	
	const DEVICE_CAPABILITY_TEST_DATA_FILE = "../../resources/device-capability.yml";
	const TEST_DATA_FILE = "../../resources/unit-test.yml";
	//const TEST_DATA_FILE = "../../resources/unit-test-single.yml";
	

	protected function setUp() {
		$this->wurflManager = $this->sharedFixture;
	}
	
	/**
	 * @test
	 * @dataProvider userAgentDeviceIdsProvider
	 */
	public function testGetDeviceForUserAgent($userAgent, $deviceId) {
		$deviceFound = $this->wurflManager->getDeviceForUserAgent ( $userAgent );
		$this->assertEquals ( $deviceId, $deviceFound->id, $userAgent );
	}
	
	public function testShouldReturnAllDevicesId() {
		$devicesId = $this->wurflManager->getAllDevicesID ();
		$this->assertContains("generic", $devicesId);
	}
	
	public function testShouldReturnWurflVersionInfo() {
		$wurflInfo = $this->wurflManager->getWURFLInfo ();
		$this->assertEquals ( "Wireless Universal Resource File v_2.1.0.1", $wurflInfo->version );
		$this->assertEquals ( "July 30, 2007", $wurflInfo->lastUpdated );
	
	}
	
	public function testGetListOfGroups() {
		$actualGroups = array ("product_info", "wml_ui", "chtml_ui", "xhtml_ui", "markup", "cache", "display", "image_format" );
		$listOfGroups = $this->wurflManager->getListOfGroups ();
		foreach ($actualGroups as $groupId) {
			$this->assertContains($groupId, $listOfGroups);
		}
	}
	
	/**
	 * 
	 * @dataProvider groupIdCapabilitiesNameProvider
	 */
	public function testGetCapabilitiesNameForGroup($groupId, $capabilitiesName) {
		$capabilities = $this->wurflManager->getCapabilitiesNameForGroup ( $groupId );
		$this->assertEquals ( $capabilitiesName, $capabilities );
	}
	
	/**
	 * 
	 * @dataProvider fallBackDevicesIdProvider
	 */
	public function testGetFallBackDevices($deviceId, $fallBacksId) {
		$fallBackDevices = $this->wurflManager->getFallBackDevices ( $deviceId );
		$fallBackDevicesId = array_map(array( $this, 'deviceId'), $fallBackDevices);
	}
	
	private function deviceId($device) {
		return $device->id;
	}
	
	
	/**
	 * 
	 * @dataProvider devicesFallBackProvider
	 */
	public function fallBackDevicesIdProvider($deviceId) {
		return array (array ("tim_igo_610_ver1", array ("i_go610_ver1", "generic" ) ) );
	
	}
	
	public static function userAgentDeviceIdsProvider() {
		$filePath = (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::TEST_DATA_FILE);
		return WURFL_TestUtils::loadUserAgentsWithIdFromFile ( $filePath );
	}
	
	public static function groupIdCapabilitiesNameProvider() {
		return array (array ("chtml_ui", array ("chtml_display_accesskey", "emoji", "chtml_can_display_images_and_text_on_same_line", "chtml_displays_image_in_center", "imode_region", "chtml_make_phone_call_string", "chtml_table_support" ) ) );
	}

}

?>