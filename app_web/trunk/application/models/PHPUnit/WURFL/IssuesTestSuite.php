<?php

require_once 'Issues/IssuesTest.php';

/**
 * Static test suite.
 */
class WURFL_IssuesTestSuite extends PHPUnit_Framework_TestSuite {
	
	protected $wurflManager;
	
	const CACHE_DIR = "../../resources/cache";
	const RESOURCES_DIR = "../../resources";
	
	private $configurationFile;
	
	private $wurflManagerFactory;
	
	public function setUp() {
		$resourcesDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::RESOURCES_DIR;
		$cacheDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		$this->createCacheDir ( $cacheDir );
		$config = new WURFL_Configuration_InMemoryConfig ();
		$config->wurflFile ( $resourcesDir . "/wurfl-latest.zip" )->wurflPatch ( $resourcesDir . "/web_browsers_patch.xml" )->persistence ( "memcache", array ("host" => "127.0.0.1", "port" => "11211" ) );
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
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'Issues Test Suite' );
		$this->addTestSuite ( 'WURFL_Issues_IssuesTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

