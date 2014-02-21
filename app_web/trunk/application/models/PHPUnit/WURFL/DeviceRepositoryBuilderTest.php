<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname ( __FILE__ ) . '/classautoloader.php';

/**
 * WURFL_DeviceRepositoryBuilder test case.
 */
class DeviceRepositoryBuilderTest extends PHPUnit_Framework_TestCase {
	
	const WURFL_FILE = "../../resources/wurfl.xml";
	const PATCH_FILE = "../../resources/web_browsers_patch.xml";
	
	const CACHE_DIR = "../../resources/cache";
	
	private $deviceRepositoryBuilder;
	
	public function __construct() {
		$cacheDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		$this->createCacheDir ( $cacheDir );
		
		$persistenceProvider = $this->createPersistenceProvider ( $cacheDir );
		
		$context = new WURFL_Context ( $persistenceProvider );
		
		$userAgentHandlerChain = WURFL_UserAgentHandlerChainFactory::createFrom ( $context );
		$devicePatcher = new WURFL_Xml_DevicePatcher ();
		$this->deviceRepositoryBuilder = new WURFL_DeviceRepositoryBuilder ( $persistenceProvider, $userAgentHandlerChain, $devicePatcher );
	}
	
	public function __destruct() {
		$cacheDir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		$this->removeCacheDir ( $cacheDir );
	}
	
	/**
	 * Tests WURFL_DeviceRepositoryBuilder->build()
	 */
	public function testBuild() {
		$wurflFile = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::WURFL_FILE;
		$patchFile = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::PATCH_FILE;
		$infoIterator = new WURFL_Xml_VersionIterator ( $wurflFile );
		$deviceIterator = new WURFL_Xml_DeviceIterator ( $wurflFile );
		$pathIterators = $this->patchIterators(array($patchFile));
		
		$deviceRepository = $this->deviceRepositoryBuilder->build ( $infoIterator, $deviceIterator, $pathIterators );
		$this->assertNotNull ( $deviceRepository );
		$this->assertEquals ( "www.wurflpro.com - 2010-02-03 10:31:00", $deviceRepository->getVersion () );
		
		$genericDevice = $deviceRepository->getDevice ( "generic" );
		$this->assertNotNull ( $genericDevice, "generic device is null" );
	}
	
	private function patchIterators($patchFiles) {
		$patchIterators = array();
		foreach ($patchFiles as $patchFile) {
			$patchIterators[] = new WURFL_Xml_DeviceIterator($patchFile);
		}
		return $patchIterators;
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

