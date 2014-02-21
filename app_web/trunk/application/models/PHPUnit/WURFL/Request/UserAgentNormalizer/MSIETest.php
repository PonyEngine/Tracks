<?php

require_once 'BaseTest.php';

/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_MSIETest extends WURFL_Request_UserAgentNormalizer_BaseTest {

	const MSIE_USERAGENTS_FILE = "msie.txt";
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->normalizer = new WURFL_Request_UserAgentNormalizer_MSIE();
	}
	
	
	/**
	 * @test
	 * @dataProvider msieUserAgentsDataProvider
	 *
	 */
	function shoudRemoveAllTheCharactersAfterTheMinorVersion($userAgent, $expected) {
		$this->assertNormalizeEqualsExpected($userAgent, $expected);			
	}
		
	
	
	function msieUserAgentsDataProvider() {
		return $this->userAgentsProvider(self::MSIE_USERAGENTS_FILE);
	}
	
	

}

