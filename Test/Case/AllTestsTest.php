<?php
/**
 * AllTests class
 *
 */
class AllTests extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {

		$suite = new PHPUnit_Framework_TestSuite('All Tests');
		$suite->addTestFile(__DIR__.'Controller'.DS. 'ImageControllerTest.php');
		$suite->addTestFile(__DIR__.'Model'.'ImageTest.php');
		$suite->addTestFile(__DIR__.'Model'.DS.'Behavior'.DS.'UploadBehaviorTest.php');
		$suite->addTestFile(__DIR__.'View'.DS.'Helper'.DS.'ImageTableHelperTest.php');
		return $suite;
	}
}
