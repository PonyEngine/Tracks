<?php


require_once 'BaseTest.php';

/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_ChromeTest extends WURFL_Request_UserAgentNormalizer_BaseTest  {
	
	const CHROME_USERAGENTS_FILE = "chrome.txt";
	
	function setUp() {		
		$this->normalizer = new WURFL_Request_UserAgentNormalizer_Chrome();
	}
	

	/**
	 * @test
	 * @dataProvider chromeUserAgentsDataProvider
	 *
	 */
	function shoudReturnOnlyFirefoxStringWithTheMajorVersion($userAgent, $expected) {
		$this->assertNormalizeEqualsExpected($userAgent, $expected);
	}
		
	
	function chromeUserAgentsDataProvider() {
		return $this->userAgentsProvider(self::CHROME_USERAGENTS_FILE);
	}
		
		
}

