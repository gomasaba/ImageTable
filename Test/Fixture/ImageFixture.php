<?php
/**
 * ImageFixture
 *
 */
class ImageFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'length' => 150),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'groupname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'filename' => array('type' => 'string', 'null' => false, 'length' => 150),
		'type' => array('type' => 'string', 'null' => false, 'length' => 32),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id'), 'images_foreign_key' => array('unique' => false, 'column' => 'foreign_key'), 'images_model' => array('unique' => false, 'column' => 'model')),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'model' => 'Post',
			'foreign_key' => 1,
			'groupname' => 'main',
			'filename' => 'test.jpg',
			'type' => 'jpg'
		),
	);
}
