<?php
require_once dirname ( __FILE__ ) . '/../classautoloader.php';

/**
 *  test case.
 */
class WURFL_Configuration_InMemoryConfigTest extends PHPUnit_Framework_TestCase {
	
	
	public function testShoudCreateConfiguration() {
		$config = new WURFL_Configuration_InMemoryConfig();
		$params = array("host"=>"127.0.0.1");
		$config->wurflFile("wurfl.xml")->wurflPatch("new_web_browsers_patch.xml")->wurflPatch("spv_patch.xml")
			->persistence("memcache", $params)
			->cache("file", array("dir" => "./cache"));
		
		$this->assertNotNull($config->persistence);
		
		$this->assertEquals("wurfl.xml", $config->wurflFile);
		$this->assertEquals(array("new_web_browsers_patch.xml", "spv_patch.xml"), $config->wurflPatches);
		
		$persistence = $config->persistence;
		$this->assertEquals("memcache", $persistence["provider"]);
		$this->assertEquals(array("host"=>"127.0.0.1"), $persistence["params"]);
		
		$cache = $config->cache;
		$this->assertEquals("file", $cache["provider"]);
		$this->assertEquals(array("dir"=>"./cache"), $cache["params"]);
		
		
	}

	
	
}

?>