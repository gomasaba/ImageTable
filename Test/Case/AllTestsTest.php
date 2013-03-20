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
		$suite->addTestFile(__DIR__.DS.'Controller'.DS. 'ImageControllerTest.php');
		$suite->addTestFile(__DIR__.DS.'Model'.'ImageTest.php');
		$suite->addTestFile(__DIR__.DS.'Model'.DS.'Behavior'.DS.'UploadBehaviorTest.php');
		$suite->addTestFile(__DIR__.DS.'View'.DS.'Helper'.DS.'ImageTableHelperTest.php');
		return $suite;
	}
}
