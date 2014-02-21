<?php

require_once dirname ( __FILE__ ) . '/../classautoloader.php';

/**
 *  test case.
 */
class WURFL_Configuration_ArrayConfigTest extends PHPUnit_Framework_TestCase {
	
	private $arrayConfig;
	

	
	function setUp() {
		$configurationFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-array-config.php";
		$this->arrayConfig = new WURFL_Configuration_ArrayConfig($configurationFile);		
	}
	
	
	/**
	 * @expectedException InvalidArgumentException
	 *
	 */
	public function testShoudThrowInvalidArgumentExceptionForNullConfigurationFilePath() {				
		$configurationFile = null;
		$arrayConfig = new WURFL_Configuration_ArrayConfig($configurationFile);
		$this->assertNotNull($arrayConfig);
	}
	
	public function testShouldCreateAConfigFormArrayFile() {
		$resourcesDir = dirname(__FILE__) . "/../../resources";	
		$wurflFile = $resourcesDir . "/wurfl-regression.zip";		
		$this->assertEquals($wurflFile, $this->arrayConfig->wurflFile);
		$expectedWurlPatches = array("$resourcesDir/new_web_browsers_patch.xml", "$resourcesDir/spv_patch.xml");
		$this->assertAttributeEquals($expectedWurlPatches, "wurflPatches", $this->arrayConfig);	
			
	}
	
	
	public function testShoudCreatePersistenceConfiguration() {
		$persistence = $this->arrayConfig->persistence;
		$this->assertEquals("memcache", $persistence["provider"]);
		$this->assertArrayHasKey("params", $persistence);
	}

	
	
}

