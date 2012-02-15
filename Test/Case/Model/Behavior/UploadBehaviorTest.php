<?php
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('Image', 'ImageTable.Model');
App::uses('UploadBehavior', 'ImageTable.Model/Behavior');

/**
 * Test Model
 *
 */
class Post extends Model{

/**
 * name property
 *
 */
	public $name = 'Post';



	public $hasOne = array(
		'MainPhoto' => array(
			'className' => 'Image',
			'foreignKey' => 'foreign_key',
			'conditions' => array('MainPhoto.model' => 'Post', 'MainPhoto.groupname' => 'main'),
			'dependent' => true,
		),
	);

	public $hasMany = array(
		'PhotoAlbum' => array(
			'className' => 'Image',
			'foreignKey' => 'foreign_key',
			'conditions' => array('PhotoAlbum.model' => 'Post', 'PhotoAlbum.groupname' => 'album'),
			'dependent' => true,
		),
	);

}
/**
 * Test Image Model
 *
 */
class Image extends Model{

/**
 * name property
 *
 */
	public $name = 'Image';


	public $actsAs = array(
		'TestUpload'=>array(
				'thumbnail'=>array(
					'thumb_s' => array(
						'w' => 100,
						'h' => 75,
						'crop' => true,
					),
					'thumb_m' => array(
						'w' => 300,
						'h' => 200,
						'crop' => false,
					),
				)
		),
	);
}
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



class UploadBehaviorTestCase extends CakeTestCase {


/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
			'plugin.image_table.post',
			'plugin.image_table.image',
	);


/**
 * Method executed before each test
 *
 */
	public function setUp() {
		Configure::write('ImageTable.upload_base',TMP.'tests');
		$this->Post = ClassRegistry::init('Post');
		parent::setUp();
	}

/**
 * Method executed after each test
 *
 */
	public function tearDown() {
		unset($this->Post);
		parent::tearDown();
	}

/**
 * test setup
 * expectedException CakeException 
 * expectedExceptionMessage imagine.phar not found.
 */
 	public function testsetup(){
 		// $this->Post->MainPhoto->save(array('test'=>'dammy'));
 	}

/**
 * test
 * @todo
 */
	public function testgetPath_False(){
		$this->assertFalse($this->Post->MainPhoto->Behaviors->TestUpload->getPath(new Model()));
 	}
/**
 * test
 * @todo
 */
	public function testgetPath(){
		$this->Post->id = 1;
		$expects = TMP.'tests'.DS.'1';
		$actual = $this->Post->MainPhoto->Behaviors->TestUpload->getPath($this->Post);
		$this->assertEqual($expects,$actual);
		$this->assertTrue(is_dir($expects));
		rmdir($expects);
 	}
/**
 * test
 * @todo
 */
	public function testprepare(){
		$this->Post->MainPhoto->data['MainPhoto'] = array(
			'groupname' => 'main',
			'model' => 'Post',
			'file' => array(
					'name' => 'test.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp',
					'error' => 0,
					'size' => 827000,
			),
		);
		$expects = array(
			'filename'=>'test.jpg',
			'type'=>'jpg',
			'groupname' => 'main',
			'model' => 'Post',
			'file' => array(
					'name' => 'test.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp',
					'error' => 0,
					'size' => 827000,
			),
		);
		$this->Post->MainPhoto->Behaviors->TestUpload->prepare($this->Post->MainPhoto);
		$this->assertEqual($expects,$this->Post->MainPhoto->data['MainPhoto']);
 	}
/**
 * test
 * @todo
 */
	public function testprocess(){
		$this->Post->MainPhoto->id = 1;
		$file = CakePlugin::path('ImageTable').'Test'.DS.'test.jpg';
		$this->Post->MainPhoto->data['MainPhoto'] = array(
			'groupname' => 'main',
			'model' => 'Post',
			'file' => array(
					'name' => 'test.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => $file,
					'error' => 0,
					'size' => 827000,
			),
		);
		$this->Post->MainPhoto->Behaviors->TestUpload->prepare($this->Post->MainPhoto);
		$this->Post->MainPhoto->Behaviors->TestUpload->process($this->Post->MainPhoto);
		$expects_1 = TMP.'tests'.DS.'1'.DS.'test.jpg';
		$expects_2 = TMP.'tests'.DS.'1'.DS.'thumb_s_test.jpg';
		$expects_3 = TMP.'tests'.DS.'1'.DS.'thumb_m_test.jpg';

		$this->assertTrue(file_exists($expects_1));
		$this->assertTrue(file_exists($expects_2));		
		$this->assertTrue(file_exists($expects_3));
		@unlink($expects_1);
		@unlink($expects_2);
		@unlink($expects_3);
		@rmdir(TMP.'tests'.DS.'1');
 	}
/**
 * test
 * @todo
 */
 	public function testsaveAll(){
		$file = CakePlugin::path('ImageTable').'Test'.DS.'test.jpg';
 		$post = array(
 			'Post'=>array(
 				'name'=>'test article title',
	 		),
	 		'MainPhoto'=>array(
				'groupname' => 'main',
				'model' => 'Post',
				'file' => array(
						'name' => 'test.jpg',
						'type' => 'image/jpeg',
						'tmp_name' => $file,
						'error' => 0,
						'size' => 827000,
				),
			),
		);

		$this->assertTrue($this->Post->saveAll($post,array('validate'=>'first')));
 		$result = $this->Post->find('first',array(
	 						'conditions'=>array(
		 							'Post.id'=>$this->Post->getLastInsertID()
				 			)));
		$phto_id = $result['MainPhoto']['id'];
		$expects_1 = TMP.'tests'.DS.$phto_id.DS.'test.jpg';
		$expects_2 = TMP.'tests'.DS.$phto_id.DS.'thumb_s_test.jpg';
		$expects_3 = TMP.'tests'.DS.$phto_id.DS.'thumb_m_test.jpg';

		$this->assertTrue(file_exists($expects_1));
		$this->assertTrue(file_exists($expects_2));		
		$this->assertTrue(file_exists($expects_3));
		@unlink($expects_1);
		@unlink($expects_2);
		@unlink($expects_3);
		@rmdir(TMP.'tests'.DS.$phto_id);
 	}
/**
 * test
 * @todo
 */
 	public function testsaveAllMultiFile(){
		$file = CakePlugin::path('ImageTable').'Test'.DS.'test.jpg';
 		$post = array(
 			'Post'=>array(
 				'name'=>'test article title',
	 		),
	 		'PhotoAlbum'=>array(
	 			array(
					'groupname' => 'album',
					'model' => 'Post',
					'file' => array(
							'name' => 'test.jpg',
							'type' => 'image/jpeg',
							'tmp_name' => $file,
							'error' => 0,
							'size' => 827000,
					),
		 		),
	 			array(
					'groupname' => 'album',
					'model' => 'Post',
					'file' => array(
							'name' => 'test.jpg',
							'type' => 'image/jpeg',
							'tmp_name' => $file,
							'error' => 0,
							'size' => 827000,
					),
		 		),
	 			array(
					'groupname' => 'album',
					'model' => 'Post',
					'file' => array(
							'name' => 'test.jpg',
							'type' => 'image/jpeg',
							'tmp_name' => $file,
							'error' => 0,
							'size' => 827000,
					),
		 		),

			),
		);

		$this->assertTrue($this->Post->saveAll($post,array('validate'=>'first')));
 		$result = $this->Post->find('first',array(
	 						'conditions'=>array(
		 							'Post.id'=>$this->Post->getLastInsertID()
				 			)));

		foreach($result['PhotoAlbum'] as $album){
			$expects_1 = TMP.'tests'.DS.$album['id'].DS.'test.jpg';
			$expects_2 = TMP.'tests'.DS.$album['id'].DS.'thumb_s_test.jpg';
			$expects_3 = TMP.'tests'.DS.$album['id'].DS.'thumb_m_test.jpg';
			$this->assertTrue(file_exists($expects_1));
			$this->assertTrue(file_exists($expects_2));
			$this->assertTrue(file_exists($expects_3));			
			@unlink($expects_1);
			@unlink($expects_2);
			@unlink($expects_3);
			@rmdir(TMP.'tests'.DS.$album['id']);
		}
 	}
/**
 * test
 * @todo
 */
 	public function testsaveAllMultiFile_NotUload_NotSave(){
		$file = CakePlugin::path('ImageTable').'Test'.DS.'test.jpg';
 		$post = array(
 			'Post'=>array(
 				'name'=>'test article title',
	 		),
	 		'PhotoAlbum'=>array(
	 			array(
					'groupname' => 'album',
					'model' => 'Post',
					'file' => array(
							'name' => 'test.jpg',
							'type' => 'image/jpeg',
							'tmp_name' => $file,
							'error' => 0,
							'size' => 827000,
					),
		 		),
	 			array(
					'groupname' => 'album',
					'model' => 'Post',
					'file' => array(
							'name' => '',
							'type' => '',
							'tmp_name' => '',
							'error' => 0,
							'size' => 0,
					),
		 		),
	 			array(
					'groupname' => 'album',
					'model' => 'Post',
					'file' => array(
							'name' => '',
							'type' => '',
							'tmp_name' => '',
							'error' => 0,
							'size' => 0,
					),
		 		),

			),
		);

		$this->assertTrue($this->Post->saveAll($post,array('validate'=>'first')));
 		$result = $this->Post->find('first',array(
	 						'conditions'=>array(
		 							'Post.id'=>$this->Post->getLastInsertID()
				 			)));
		$this->assertEqual(1,count($result['PhotoAlbum']));
 	}

/**
 * test upload false image record delete
 * @todo
 */
 	public function testTransaction(){
 		$file = CakePlugin::path('ImageTable').'Test'.DS.'test.jpg';
 		$post = array(
 			'Post'=>array(
 				'name'=>'test article title',
	 		),
	 		'MainPhoto'=>array(
				'groupname' => 'main',
				'model' => 'Post',
				'file' => array(
						'name' => 'test.jpg',
						'type' => 'image/jpeg',
						'tmp_name' => $file,
						'error' => 0,
						'size' => 827000,
				),
			),
		);
		// move_upload flag false
		$this->Post->MainPhoto->Behaviors->TestUpload->move_upload_flg = false;
		$this->assertTrue($this->Post->saveAll($post,array('validate'=>'first')));
		$photo_id = $this->Post->MainPhoto->getLastInsertID();

		$this->assertFalse($this->Post->MainPhoto->exists($photo_id));

		$expects_1 = TMP.'tests'.DS.$photo_id.DS.'test.jpg';
		$expects_2 = TMP.'tests'.DS.$photo_id.DS.'thumb_s_test.jpg';
		$expects_3 = TMP.'tests'.DS.$photo_id.DS.'thumb_m_test.jpg';

		$this->assertTrue(!file_exists($expects_1));
		$this->assertTrue(!file_exists($expects_2));		
		$this->assertTrue(!file_exists($expects_3));
		@rmdir(TMP.'tests'.DS.$phto_id);

 	}

}