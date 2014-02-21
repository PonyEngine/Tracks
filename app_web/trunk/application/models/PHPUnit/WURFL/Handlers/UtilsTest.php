<?php

/**
 * WURFL_Handlers_Utils test case.
 */
class WURFL_Handlers_UtilsTest extends PHPUnit_Framework_TestCase {
	
	
	public function testIndexOfOrLength($haystack, $needle, $offset, $expected) {
		
	}
	
	
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testShouldThrowExceptionForNullString() {
		WURFL_Handlers_Utils::ordinalIndexOf ( NULL, "", 0 );
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testShouldThrowExceptionForEmptyString() {
		WURFL_Handlers_Utils::ordinalIndexOf ( "", "", 0 );
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testShouldThrowExceptionForNonNumericOrdinalVlaue() {
		WURFL_Handlers_Utils::ordinalIndexOf ( "useranget", "", "" );
	}
	
	/**
	 * @dataProvider ordinalIndexOfDataProvider
	 */
	public function testOrdinalIndexOf($haystack, $needle, $ordinal, $expectedIndex) {
		$found = WURFL_Handlers_Utils::ordinalIndexOf ( $haystack, $needle, $ordinal );
		$this->assertEquals ( $expectedIndex, $found );
	
	}
	
	public function testShouldReturnNegativeOneForInexistantChar() {
		$haystack = "Mozilla/4.0 (compatible; MSIE 4.0; Windows 95; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
		$needle = ":";
		$expected = WURFL_Handlers_Utils::ordinalIndexOf ( $haystack, $needle, 1 );
		$this->assertEquals ( - 1, $expected );
	
	}
	
	
	
	public static function ordinalIndexOfOrLengthDataProvider() {
		return array (
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.9) VZW:SCH-i760 PPC 240x320", "/", 1, 7 ), 
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", ";", 1, 23 ),
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", ";", 2, 33 ),
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", ";", 3, 49 ), 
			array ("Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)", "/", 1, 7 ),
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; GoodAccess 3.7.0.9 (PalmOS 5.1))", ";", 4, -1) 
		);
	}
	
	
	public static function ordinalIndexOfDataProvider() {
		return array (
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.9) VZW:SCH-i760 PPC 240x320", "/", 1, 7 ), 
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", ";", 1, 23 ),
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", ";", 2, 33 ),
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", ";", 3, 49 ), 
			array ("Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)", "/", 1, 7 ),
			array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; GoodAccess 3.7.0.9 (PalmOS 5.1))", ";", 4, -1) 
		);
	}
	
	public static function userAgentsWithThirdSemiColumn() {
		return array (array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.9) VZW:SCH-i760 PPC 240x320", 38 ), array ("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 1.1.4322)", 42 ), array ("Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)", 42 ) );
	}
	
	/**
	 * @dataProvider userAgentsWithFirstSlash
	 *
	 */
	public function testFirstSlash() {
		return array (array ("", 0, 0 ), array ("", 1, 1 ), array ("", 0, 1 ) );
	
	}

}

