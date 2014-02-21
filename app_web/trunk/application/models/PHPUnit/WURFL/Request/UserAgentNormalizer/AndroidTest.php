<?php

require_once 'BaseTest.php';

/**
 * test case.
 */
class WURFL_Request_UserAgentNormalizer_AndroidTest extends WURFL_Request_UserAgentNormalizer_BaseTest {
	
	const ANDROID_USERAGENTS_FILE = "android.txt";
	
	
	public function setUp() {
		$this->normalizer = new WURFL_Request_UserAgentNormalizer_Android ();
	}
	
	/**
	 * @test
	 * @dataProvider androidUserAgentsProvider
	 */
	function shoudReturnTheUserAgentdWithOutTheLanguageCode($userAgent, $expected) {
		$actual = $this->normalizer->normalize ( $userAgent );
		$this->assertEquals ( $expected, $actual , "$actual");
	}
	
	function androidUserAgentsProvider() {
		return $this->userAgentsProvider ( self::ANDROID_USERAGENTS_FILE );
	}

}

