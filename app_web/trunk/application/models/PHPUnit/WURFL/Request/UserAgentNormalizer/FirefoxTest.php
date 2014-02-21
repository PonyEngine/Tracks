<?php


require_once 'BaseTest.php';

/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_FirefoxTest extends WURFL_Request_UserAgentNormalizer_BaseTest  {
	
	const FIREFOX_USERAGENTS_FILE = "firefox.txt";
	

	function setUp() {		
		$this->normalizer = new WURFL_Request_UserAgentNormalizer_Firefox();
	}
	

	/**
	 * @test
	 * @dataProvider firefoxUserAgentsDataProvider
	 *
	 */
	function shoudReturnOnlyFirefoxStringWithTheMajorVersion($userAgent) {
		$expected = "Firefox/M.m";
		$found = $this->normalizer->normalize($userAgent);
		$this->assertEquals(strlen($expected), strlen($found), $userAgent);
		$this->assertTrue(strpos($found, "Firefox/") !== FALSE, $userAgent);
	
	}
		
	
	function firefoxUserAgentsDataProvider() {
		return $this->userAgentsProvider(self::FIREFOX_USERAGENTS_FILE);
	}
		
		
}

