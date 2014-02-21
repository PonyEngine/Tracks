<?php

/**
 * test case.
 */
class WURFL_Request_UserAgentNormalizer_SafariTest extends WURFL_Request_UserAgentNormalizer_BaseTest {
	
	const SAFARI_USERAGENTS_FILE = "safari.txt";

	function setUp() {
		$this->normalizer = new WURFL_Request_UserAgentNormalizer_Safari ();
	}
	
	/**
	 * @test
	 * @dataProvider safariUserAgentsProvider
	 */
	function shoudReturnTheTypeWithTheSafariMajorVersion($userAgent, $expected) {
		$this->assertNormalizeEqualsExpected ( $userAgent, $expected );
	}
	
	function safariUserAgentsProvider() {
		return $this->userAgentsProvider ( self::SAFARI_USERAGENTS_FILE );
	}

}

