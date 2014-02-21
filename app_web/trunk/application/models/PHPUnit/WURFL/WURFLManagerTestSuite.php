<?php

require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'WURFLManagerTest.php';
require_once 'DeviceTest.php';
require_once 'WURFLReloadingTest.php';


/**
 * Static test suite.
 */
class WURFL_WURFLManagerTestSuite extends PHPUnit_Framework_TestSuite {
	
	const RESOURCES_DIR =  "../../resources";
	const WURFL_CONFIG_FILE = "../../resources/wurfl-config.xml";
	const CACHE_DIR = "../../resources/cache";
		
	private $configurationFile;
	
	private $wurflManagerFactory;
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'WURFLManagerTestSuite' );
		
		$this->addTestSuite('WURFL_WURFLManagerTest');		
		$this->addTestSuite('WURFL_DeviceTest');
		$this->addTestSuite('WURFL_WURFLReloadingTest');
	}
	
	
	public function setUp() {
		$resourcesDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::RESOURCES_DIR;
		$cacheDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		$this->createCacheDir ( $cacheDir );
		$config = new WURFL_Configuration_InMemoryConfig();
		$config->wurflFile($resourcesDir ."/wurfl-regression.xml")
				->wurflPatch($resourcesDir ."/web_browsers_patch.xml")
				->wurflPatch($resourcesDir . "/spv_patch.xml")
				->persistence("memcache", array("host"=> "127.0.0.1", "port"=>"11211"));
		$this->wurflManagerFactory = new WURFL_WURFLManagerFactory ( $config );
		$this->sharedFixture = $this->wurflManagerFactory->create ();
	}
	
	public function tearDown() {
		$this->wurflManagerFactory->remove ();
	}
	
	private function createCacheDir($cacheDir) {
		@mkdir ( $cacheDir, 0777 );
	}
	
	private function removeCacheDir($cacheDir) {
		WURFL_FileManager::removeDir ( $cacheDir );
	}
	
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

