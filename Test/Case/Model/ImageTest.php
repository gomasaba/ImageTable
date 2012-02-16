<?php
App::uses('Image', 'ImageTable.Model');
App::uses('ModelBehavior','Model');
App::uses('UploadBehavior', 'ImageTable.Model/Behavior');

/**
 * Test UploadBehavior
 *
 * upload function overwride
 */
class TestUploadBehavior extends UploadBehavior{


	public $move_upload_flg = true;
	public $is_upload_flg = true;

	public function move_uploaded_file($filename, $destination){
		if(!empty($filename) && !empty($destination)){			
			copy($filename,$destination);
		}
		return $this->move_upload_flg;
	}

	public function is_uploaded_file($filename){
		return $this->is_upload_flg;
	}
}


Class ImageTest extends CakeTestCase{


/**
 * setUp method
 *
 * @return void
 */
	public function setUp(){
		$this->Image = ClassRegistry::init('ImageTable.Image');
		$this->Image->Behaviors->detach('ImageTable.Upload');		
		$this->Image->Behaviors->attach('TestUpload');
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

	public function _set_success_data(){
		return array(
			'groupname' => 'main',
			'model' => 'Post',
			'file' => array(
					'name' => 'test.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/phphs0sEz',
					'error' => 0,
					'size' => 1024,
				),
			);
	}
/**
 * test validation
 *s
 * @return void
 */
 	public function testValidation_Success(){
		$this->Image->set($this->_set_success_data());
		$this->assertTrue($this->Image->validates());
 	}

/**
 * test validation
 *s
 * @return void
 */
 	public function testValidation_ModelEmpty(){
		$test = $this->_set_success_data();
		$test['model'] = '';
		$this->Image->set($test);
		$this->assertFalse($this->Image->validates());
		$fields = $this->Image->invalidFields();
		$this->assertArrayHasKey('model',$fields);
 	}
/**
 * test validation
 *
 * @return void
 */
 	public function testValidation_File_Name(){
		$test = $this->_set_success_data();
		$test['file']['name'] = '日本語のファイル名.jpg';
		$this->Image->set($test);
		$this->assertFalse($this->Image->validates());
		$fields = $this->Image->invalidFields();
		$this->assertArrayHasKey('file',$fields);
 	}
/**
 * test validation
 *
 * @return void
 */
 	public function testValidation_File_Size(){
		$test = $this->_set_success_data();
		$test['file']['size'] = 1024*1024*2.1;
		$this->Image->set($test);
		$this->assertFalse($this->Image->validates());
		$fields = $this->Image->invalidFields();
		$this->assertArrayHasKey('file',$fields);
 	}
 /**
 * FilenameSuccessProvider
 *
 */
	public static function FilenameSuccessProvider() {
		return array(
					array('test.jpg','image/jpeg'),
					array('TEST.JPG','image/jpeg'),
					array('test.jpeg','image/jpeg'),
					array('test.JPEG','image/jpeg'),
					array('test.gif','image/gif'),
					array('test.GIF','image/gif'),
					array('test.png','image/png'),
					array('test.PNG','image/PNG'),
		);
	}
/**
 * test validation
 * @dataProvider FilenameSuccessProvider
 * @return void
 */
 	public function testValidation_File_Ext_OK($filename,$type){
		$test = $this->_set_success_data();
		$test['file']['name'] = $filename;
		$test['file']['type'] = $type;
		$this->Image->set($test);
		$this->assertTrue($this->Image->validates());
 	}




}