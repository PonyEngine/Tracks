<?php

/**
 *  test case.
 */
class WURFL_Configuration_XmlConfigTest extends PHPUnit_Framework_TestCase {
	
	private $xmlConfig;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$configPath = dirname(__FILE__) . "/wurfl-config.xml";
		$this->xmlConfig = new WURFL_Configuration_XmlConfig($configPath);
	}
		
	
	public function testShouldInitializeConfigurationObjectFromAbsolutePath() {
		$this->assertNotNull($this->xmlConfig);
	}
	
	
	public function testShoudAcceptFileBasedCacheConfigurationAsRelativePath() {
	}
	
	
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown ();
	}

}

