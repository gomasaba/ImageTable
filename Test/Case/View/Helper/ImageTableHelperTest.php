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
		$this->View = new View($this->Controller);

		$this->ImgTblHelper = new ImageTableHtmlHelper($this->View);
		$this->ImgTblHelper->Form = new FormHelper($this->View);
		$this->ImgTblHelper->Html = new HtmlHelper($this->View);
		$this->ImgTblHelper->request = new CakeRequest('posts/add', false);
		$this->ImgTblHelper->request->here = '/posts/add';
		$this->ImgTblHelper->request['controller'] = 'posts';
		$this->ImgTblHelper->request['action'] = 'add';
		$this->ImgTblHelper->request->webroot = '';
		$this->ImgTblHelper->request->base = '';

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
 	public function testinputform(){
 		$test = array(
			'className'=>'MainPhoto',
			'model'=>'Post',
			'groupname'=>'main',
		);
		$actual = $this->ImgTblHelper->__inputfile($test);
		$expected = array(
			'div' => array('class' => 'input number'),
			'label' => array('for' => 'ContactFoo'),
			'Foo',
			'/label',
			array('input' => array(
				'type' => 'number',
				'name' => 'data[Contact][foo]',
				'id' => 'ContactFoo',
				'step' => '0.5'
			)),
			'/div'
		);
		$this->assertTags($result, $expected);		

 	}
/**
 * input form add
 *
 */
 	public function InputFormCreate(){
 		$this->ImgTblHelper->__parseAssociation(ClassRegistry::getObject('Post')); 
 		$expects = array(
 			'hasOne'=>array(
 				'MainPhoto'=>array(
 					'model'=>'Post',
 					'groupname'=>'main',
	 			)
	 		),
 			'hasMany'=>array(
 				'PhotoAlbum'=>array(
 					'model'=>'Post',
 					'groupname'=>'main',
	 			)
	 		)
	 	);
	 	$this->assertEqual($expects,$this->ImgTblHelper->parseAssociation);

 		// $results = $this->ImgTblHelper->autoform('Post');
 	}



	
}
