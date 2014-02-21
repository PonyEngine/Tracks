<?php

require_once dirname ( __FILE__ ) . '/../classautoloader.php';

/**
 * test case.
 */
class WURFL_Hanlders_FirefoxHandlerTest extends PHPUnit_Framework_TestCase {
	
	private $firefoxHandler;
	
	function setUp() {
		$context = new WURFL_Context ( $this->persistenceProvider() );
		$userAgentNormalizer = new WURFL_Request_UserAgentNormalizer_Firefox ();
		$this->firefoxHandler = new WURFL_Handlers_FirefoxHandler ( $context, $userAgentNormalizer );
	}
	/*
	function testShoudApplyRisWithFirstSlash() {
		$userAgent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; nb-NO; rv:1.9b5) Gecko/2008032619 Firefox/3.0b5";
		$found = $this->firefoxHandler->match ( new WURFL_Request_GenericRequest($userAgent ));
		$expected = "firefox_3_0_mac_osx";
		$this->assertEquals ( $expected, $found );
	}
	*/
	
	private function persistenceProvider() {
		$datas = array();
		return new WURFL_TestUtils_MockPersistenceProvider($datas);		
	}
}

