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
		$suite->addTestFile(TESTS.'Case'.DS.'Controller'.DS. 'ImageControllerTest.php');
		$suite->addTestFile(TESTS.'Case'.DS.'Model'.'ImageTest.php');
		$suite->addTestFile(TESTS.'Case'.DS.'Model'.DS.'Behavior'.DS.'UploadBehaviorTest.php');
		$suite->addTestFile(TESTS.'Case'.DS.'View'.DS.'Helper'.DS.'ImageTableHelperTest.php');
		return $suite;
	}
}
