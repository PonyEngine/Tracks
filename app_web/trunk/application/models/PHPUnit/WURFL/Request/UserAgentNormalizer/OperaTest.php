<?php

require_once 'BaseTest.php';

/**
 * test case.
 */
class WURFL_Request_UserAgentNormalizer_OperaTest extends WURFL_Request_UserAgentNormalizer_BaseTest {
	
	const OPERA_USERAGENTS_FILE = "opera.txt";
	
	function setUp() {
		$this->normalizer = new WURFL_Request_UserAgentNormalizer_Opera ();
	}
	
	/**
	 * @test
	 * @dataProvider operaUserAgentsDataProvider
	 *
	 */
	function shoudReturnOnlyOperaStringWithTheMajorVersion($userAgent, $expected) {
		$this->assertNormalizeEqualsExpected ( $userAgent, $expected );	
	}
	
	function operaUserAgentsDataProvider() {
		return $this->userAgentsProvider ( self::OPERA_USERAGENTS_FILE );
	}

}

