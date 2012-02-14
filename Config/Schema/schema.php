<?php 
class ImageTableSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $images = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 11, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'length' => 150),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'groupname' => array('type' => 'string', 'null' => true, 'length' => 64),
		'filename' => array('type' => 'string', 'null' => false, 'length' => 150),
		'type' => array('type' => 'string', 'null' => false, 'length' => 32),
		'indexes' => array('PRIMARY' => array('unique' => true, 'column' => 'id'), 'images_foreign_key' => array('unique' => false, 'column' => 'foreign_key'), 'images_model' => array('unique' => false, 'column' => 'model')),
		'tableParameters' => array()
	);
}
