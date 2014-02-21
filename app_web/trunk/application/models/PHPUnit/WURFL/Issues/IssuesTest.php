<?php

require_once dirname ( __FILE__ ) . '/../classautoloader.php';

class WURFL_Issues_IssuesTest extends PHPUnit_Framework_TestCase {
	
	
	/**
	 * @test
	 * @dataProvider issuesProvider
	 */
	public function testIssues($userAgent, $deviceId) {
		$deviceFound = $this->sharedFixture->getDeviceForUserAgent ( $userAgent );
		$this->assertEquals ( $deviceId, $deviceFound->id, $userAgent );
	}
	
	const ISSUES_FILE = "issues.txt";
	public static function issuesProvider() {
		$fullTestFilePath = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . self::ISSUES_FILE;
		$lines = file ( $fullTestFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$map = array ();
		foreach ( $lines as $line ) {
			if (strpos ( $line, "#" ) !== 0) {
				$map [] = explode ( "=", $line );
			}
		}
		return $map;
	}
	
	

}

?>