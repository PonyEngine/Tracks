<?php

class WURFL_Xml_DeviceIteratorTest extends PHPUnit_Framework_TestCase {
	
	const WURFL_FILE = "../../../resources/wurfl.xml";
	const CACHE_DIR = "../../../resources/cache";
	
	private $filePersistence;
	
	public function setUp() {
		$cacheDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		$this->createCacheDir($cacheDir);
		$this->filePersistence = $this->createPersistenceProvider ($cacheDir);
	}
	
	public function tearDown() {
		$cacheDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		$this->removeCacheDir ($cacheDir);
		$this->filePersistence = null;
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testShouldLaunchExceptionForInvalidInputFile() {
		$wurflFile = "";
		$deviceIterator = new WURFL_Xml_DeviceIterator ( $wurflFile );
	
	}
	
	
	public function testIterator() {
		$wurflFile = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::WURFL_FILE;
		
		$deviceIterator = new WURFL_Xml_DeviceIterator ( $wurflFile );
		
		foreach ( $deviceIterator as $device ) {
			//$processedDevice = $this->process ( $device );
		}
	}
	
	private function process($device) {
		return $this->filePersistence->save ( $device->id, $device );
	}
	
	private function createPersistenceProvider($cacheDir) {
		$params = array ("dir" => $cacheDir );
		return new WURFL_Xml_PersistenceProvider_FilePersistenceProvider ( $params );
	}
	
	private function createCacheDir($cacheDir) {
		@mkdir ( $cacheDir, 0777 );
	}
	
	private function removeCacheDir($cacheDir) {
		WURFL_FileManager::removeDir ( $cacheDir );
	}

}

?>