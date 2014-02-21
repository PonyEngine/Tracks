<?php

require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'WURFL/WURFLManagerTestSuite.php';
require_once 'WURFL/ReloaderTestSuite.php';
require_once 'WURFL/ConfigurationTestSuite.php';
require_once 'WURFL/HandlersTestSuite.php';
require_once 'WURFL/XmlTestSuite.php';
require_once 'WURFL/Request/UserAgentNormalizerTestSuite.php';
require_once 'WURFL/IssuesTestSuite.php';

/**
 * Static test suite.
 */
class WURFLTestsSuite extends PHPUnit_Framework_TestSuite {
	
	
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'WURFLTestsSuite' );
		
		$this->addTestSuite ( 'WURFL_ConfigurationTestSuite' );
		//$this->addTestSuite ( 'WURFL_XmlTestSuite' );
		$this->addTestSuite ( 'WURFL_WURFLManagerTestSuite' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizerTestSuite' );
		$this->addTestSuite ( 'WURFL_HandlersTestSuite' );
		$this->addTestSuite ( 'WURFL_IssuesTestSuite' );
		//$this->addTestSuite ( 'WURFL_ReloaderTestSuite' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

