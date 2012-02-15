<?php
App::uses('Image', 'ImageTable.Model');
Class ImageTest extends CakeTestCase{


/**
 * setUp method
 *
 * @return void
 */
	public function setUp(){
		$this->Image = ClassRegistry::init('Image');
		parent::setUp();
	}
/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Image);
		ClassRegistry::flush();
		parent::tearDown();
	}



/**
 * test validation
 *
 * @return void
 */
 	public function Validation(){
		$test = array(
		'Image' => array(
			'groupname' => 'main',
			'model' => 'Post',
			'file' => array(
					'name' => 'Lighthouse.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/phphs0sEz',
					'error' => 0,
					'size' => 561276000000,
				),
			),
		);
		$this->Image->set($test);
		var_dump($this->Image->data);
		
		$actual = $this->Image->validates();
		var_dump($this->Image->data);
		var_dump($actual);
 	}



}