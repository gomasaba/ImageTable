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
		$suite->addTestDirectory(TESTS .'Case'.DS. 'Controller');
		$suite->addTestDirectory(TESTS .'Case'.DS. 'Model');
		return $suite;
	}
}
