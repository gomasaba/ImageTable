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

		$suite = new CakeTestSuite('All Tests');
		$suite->addTestDirectory(dirname(__FILE__) .DS. 'Controller');
		$suite->addTestDirectory(dirname(__FILE__) .DS. 'View/Helper');
		$suite->addTestDirectory(dirname(__FILE__) .DS. 'Model');
		$suite->addTestDirectory(dirname(__FILE__) .DS. 'Model/Behavior');
		return $suite;
	}
}
