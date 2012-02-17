<?php
App::uses('ClassRegistry', 'Utility');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('Model', 'Model');
App::uses('Security', 'Utility');
App::uses('CakeRequest', 'Network');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('Router', 'Routing');

App::uses('AppHelper', 'View/Helper');
App::uses('ImageTableHtmlHelper', 'ImageTable.View/Helper');



/**
 * PostsTestController class
 *
 * @package	   cake
 * @package       Cake.Test.Case.View.Helper
 */
class PostsController extends Controller {

/**
 * name property
 *
 * @var string 'ContactTest'
 */
	public $name = 'Posts';

/**
 * uses property
 *
 * @var mixed null
 */
	public $uses = array('Post');
}
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


class ImageTableHtmlHelperTestCase extends CakeTestCase {
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
		parent::setUp();

		Configure::write('App.base', '');
		$this->Controller = new PostsController();
		$this->Controller->request = new CakeRequest('posts/add', false);
		$this->Controller->request->here = '/posts/add';
		$this->Controller->request['controller'] = 'posts';
		$this->Controller->request['action'] = 'add';
		$this->Controller->request->webroot = '';
		$this->Controller->request->base = '';

		$this->View = new View($this->Controller);

		$this->ImgTblHelper = new ImageTableHtmlHelper($this->View);
		$this->ImgTblHelper->Form = new FormHelper($this->View);
		$this->ImgTblHelper->Form->Html = new HtmlHelper($this->View);
		$this->ImgTblHelper->Html = new HtmlHelper($this->View);

		ClassRegistry::addObject('Post', new Post());

		Configure::write('ImageTable.upload_base',TMP.'tests');
	}

/**
 * Method executed after each test
 *
 */
	public function tearDown() {
		unset($this->ImgTblHelper, $this->Controller, $this->View);
		parent::tearDown();
	}
/**
 * input form add
 *
 */
 	public function testPrepare(){
 		$test = array(
			'MainPhoto' => array(
				'className' => 'Image',
				'foreignKey' => 'foreign_key',
				'conditions' => array('MainPhoto.model' => 'Post', 'MainPhoto.groupname' => 'main'),
				'dependent' => true,
			),
			'SubPhoto' => array(
				'className' => 'Image',
				'foreignKey' => 'foreign_key',
				'conditions' => array('SubPhoto.model' => 'Post', 'SubPhoto.groupname' => 'sub'),
				'dependent' => true,
			),
		);
		$expects = array(
			array(
				'className'=>'MainPhoto',
				'model'=>'Post',
				'groupname'=>'main',
			),
			array(
				'className'=>'SubPhoto',
				'model'=>'Post',
				'groupname'=>'sub',
			),
		);
		$this->assertEquals($expects,$this->ImgTblHelper->prepare($test));
 	}
/**
 * input form add
 *
 */
 	public function testinputform_Add(){
		$_SERVER['REQUEST_METHOD'] = 'get';
		$render = $this->ImgTblHelper->autoform('Post');
		//1
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'file',
				'name' => 'data[MainPhoto][file]',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[MainPhoto][model]',
				'value' => 'Post',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[MainPhoto][groupname]',
				'value' => 'main',
			)
		);
		$this->assertTag($expected,$render);
		//2
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'file',
				'name' => 'data[PhotoAlbum][1][file]',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[PhotoAlbum][1][model]',
				'value' => 'Post',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[PhotoAlbum][1][groupname]',
				'value' => 'album',
			)
		);
		$this->assertTag($expected,$render);

 	}

/**
 * input form add
 *
 */
 	public function testinputform_Add_Array(){
		$_SERVER['REQUEST_METHOD'] = 'get';
		$this->ImgTblHelper->autoRenderString = false;
		$render = $this->ImgTblHelper->autoform('Post');
		$this->assertTrue(is_array($render));
		//1
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'file',
				'name' => 'data[MainPhoto][file]',
			)
		);
		$this->assertTag($expected,$render[0]);
	} 	
/**
 * input form add
 *
 */
 	public function testinputform_Post_Error(){
		$_SERVER['REQUEST_METHOD'] = 'post';
		$this->ImgTblHelper->request->data = array(
	 		'MainPhoto'=>array(
				'groupname' => 'main',
				'model' => 'Post',
				'file' => array(
						'name' => 'test.jpg',
						'type' => 'image/jpeg',
						'tmp_name' => '/tmp/dammy',
						'error' => 0,
						'size' => 827000,
				),		 	),
	 		'PhotoAlbum'=>array(
	 			array(
				'groupname' => 'main',
				'model' => 'Post',
				'file' => array(
						'name' => 'test.jpg',
						'type' => 'image/jpeg',
						'tmp_name' => '/tmp/dammy',
						'error' => 0,
						'size' => 827000,
						),	 				
		 		),
		 	),
	 	);
	 	$render = $this->ImgTblHelper->autoform('Post');
		//1
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'file',
				'name' => 'data[MainPhoto][file]',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[MainPhoto][model]',
				'value' => 'Post',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[MainPhoto][groupname]',
				'value' => 'main',
			)
		);
		$this->assertTag($expected,$render);
		//2
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'file',
				'name' => 'data[PhotoAlbum][1][file]',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[PhotoAlbum][1][model]',
				'value' => 'Post',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[PhotoAlbum][1][groupname]',
				'value' => 'album',
			)
		);
		$this->assertTag($expected,$render);
 	}

/**
 * input form add
 *
 */
 	public function testinputform_Edit(){
 		// $this->Controller->request->params['models']['Post'] = array();
 		$this->ImgTblHelper->request->data = array(
 			'Post'=>array(
 				'id'=>1,
 				'name'=>'dammy'
	 		),
	 		'MainPhoto'=>array(
	 			'id'=>2,
	 			'model'=>'Post',
	 			'foreign_key'=>1,
	 			'groupname'=>'main',
	 			'filename'=>'test.jpg',
	 			'type'=>'jpg'
		 	),
	 		'PhotoAlbum'=>array(
	 			array(
		 			'id'=>3,
		 			'model'=>'Post',
		 			'foreign_key'=>1,
		 			'groupname'=>'album',
		 			'filename'=>'test.jpg',
		 			'type'=>'jpg'
		 		),
	 			array(
		 			'id'=>4,
		 			'model'=>'Post',
		 			'foreign_key'=>1,
		 			'groupname'=>'album',
		 			'filename'=>'test.jpg',
		 			'type'=>'jpg'
		 		)
		 	),
	 	);
		$render = $this->ImgTblHelper->autoform('Post',array('prefix'=>'thumb_s'));

		//MainImage
		$this->assertRegExp('/2\/thumb_s_test\.jpg/',$render);
		$expected = array(
			'tag' => 'a',
			'attributes'=>array(
				'href'=>'/ImageTable/image/delete/2',
			)
		);
		$this->assertTag($expected,$render);
		//MainImage hidden
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[MainPhoto][id]','value'=>'2'));
		$this->assertTag($expected,$render);
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[MainPhoto][model]','value'=>'Post'));
		$this->assertTag($expected,$render);
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[MainPhoto][filename]','value'=>'test.jpg'));
		$this->assertTag($expected,$render);

		//Album 1
		$this->assertRegExp('/3\/thumb_s_test\.jpg/',$render);
		$expected = array(
			'tag' => 'a',
			'attributes'=>array(
				'href'=>'/ImageTable/image/delete/3',
			)
		);
		//Album 1 hidden
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[PhotoAlbum][3][id]','value'=>'3'));
		$this->assertTag($expected,$render);
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[PhotoAlbum][3][model]','value'=>'Post'));
		$this->assertTag($expected,$render);
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[PhotoAlbum][3][filename]','value'=>'test.jpg'));
		$this->assertTag($expected,$render);


		//Album 2
		$this->assertRegExp('/4\/thumb_s_test\.jpg/',$render);
		$expected = array(
			'tag' => 'a',
			'attributes'=>array(
				'href'=>'/ImageTable/image/delete/4',
			)
		);
		//Album 2 hidden
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[PhotoAlbum][4][id]','value'=>'4'));
		$this->assertTag($expected,$render);
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[PhotoAlbum][4][model]','value'=>'Post'));
		$this->assertTag($expected,$render);
		$expected = array('tag' => 'input','attributes'=>array('type'=>'hidden','name'=>'data[PhotoAlbum][4][filename]','value'=>'test.jpg'));
		$this->assertTag($expected,$render);



		//Album New
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'file',
				'name' => 'data[PhotoAlbum][5][file]',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[PhotoAlbum][5][model]',
				'value' => 'Post',
			)
		);
		$this->assertTag($expected,$render);
		$expected = array(
			'tag' => 'input',
			'attributes'=>array(
				'type' => 'hidden',
				'name' => 'data[PhotoAlbum][5][groupname]',
				'value' => 'album',
			)
		);
		$this->assertTag($expected,$render);
 	}
/**
 * input form add
 *
 */
 	public function testimage(){
 		$test = array(
			'id'=>4,
			'model'=>'Post',
			'foreign_key'=>1,
			'groupname'=>'album',
			'filename'=>'test.jpg',
			'type'=>'jpg'
	 	);
	 	$render = $this->ImgTblHelper->image($test,array('w'=>100,'h'=>50));
	 	$this->assertRegExp('/4\/100\/50\/test.jpg/',$render);

	 	$render = $this->ImgTblHelper->image($test,array('w'=>200,'h'=>100,'class'=>'thumb','alt'=>'this is thumbnail'));
	 	$this->assertRegExp('/4\/200\/100\/test.jpg/',$render);
		$expected = array(
			'tag' => 'img',
			'attributes'=>array(
				'class' => 'thumb',
				'alt' =>'this is thumbnail'
			)
		);
		$this->assertTag($expected,$render);


	 	$render = $this->ImgTblHelper->image($test,array('class'=>'Origin'));
	 	$this->assertRegExp('/4\/test.jpg/',$render);
		$expected = array(
			'tag' => 'img',
			'attributes'=>array(
				'class' => 'Origin',
			)
		);
		$this->assertTag($expected,$render);

 	}

	
}
