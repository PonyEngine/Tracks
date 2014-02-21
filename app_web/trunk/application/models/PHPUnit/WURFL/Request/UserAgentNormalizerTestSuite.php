<?php

require_once 'UserAgentNormalizer/ChromeTest.php';
require_once 'UserAgentNormalizer/AndroidTest.php';
require_once 'UserAgentNormalizer/OperaTest.php';
require_once 'UserAgentNormalizer/SafariTest.php';
require_once 'UserAgentNormalizer/FirefoxTest.php';
require_once 'UserAgentNormalizer/MSIETest.php';

/**
 * Static test suite.
 */
class WURFL_Request_UserAgentNormalizerTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'UserAgentNormalizerSuite' );		
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_ChromeTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_OperaTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_FirefoxTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_MSIETest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_SafariTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_AndroidTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

