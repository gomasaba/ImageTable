<?php
App::uses('Router', 'Routing');
App::uses('Controller', 'Controller');

App::uses('ImageController', 'ImageTable.Controller');
App::uses('Upload', 'ImageTable.Model/Behavior');


/**
 * TestImageController
 *
 */
class TestImageController extends ImageController {

	public $name = 'Image';

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}


/**
 * ImageControllerTestCase
 *
 */
class ImageControllerTestCase extends CakeTestCase {

	public $fixtures = array(
			'plugin.image_table.post',
			'plugin.image_table.image',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('ImageTable.upload_base',TMP.'tests');
		$request = new CakeRequest(null, false);
		$response  = new CakeResponse();
		$this->Controller = new TestImageController($request,$response);
		$this->Controller->constructClasses();
	}
/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Controller);
		unset($this->Image);
		ClassRegistry::flush();
		parent::tearDown();
	}

	public function _set_file(){
		$file = CakePlugin::path('ImageTable').'Test'.DS.'test.jpg';
		if(!is_dir(TMP.'tests'.DS.'1')) mkdir(TMP.'tests'.DS.'1');
		$to = TMP.'tests'.DS.'1'.DS.'test.jpg';
		copy($file,$to);		
	}
	
	public function _unset_file(){
		$dir = TMP.'tests'.DS.'1';
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($file->isFile()) {
				@unlink($file->getPathname());
            }
        }
	}



/**
 * @test Plugin routing
 * @see 
 */
	public function testPluginRoute(){
		require_once CakePlugin::path('ImageTable').'Config'.DS.'routes.php';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parse('/image/1/100/75/test.jpg');
		$expects = array(
			'named' => array(
			),
			'pass' =>array(
				0 => '1',
				1 => '100',
				2 => '75',
				3 => 'test.jpg',
				),
			'controller' => 'image',
			'action' => 'display',
			'plugin' => 'ImageTable',
		);
		$this->assertEquals($expects,$result);

		$result = Router::parse('/image/delete/1');
		$expects = array(
			'named' => array(
			),
			'pass' =>array(
				0 => '1',
				),
			'controller' => 'image',
			'action' => 'delete',
			'plugin' => 'ImageTable',
		);
		$this->assertEquals($expects,$result);
	}
/**
 * @test display method
 * @see 
 */
	public function testDisplay_File_NotFound(){
		require_once CakePlugin::path('ImageTable').'Config'.DS.'routes.php';
		$this->Controller->startupProcess();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$results = $this->Controller->display('1',200,100,'notfound.jpg');
		$this->assertInstanceOf('CakeResponse',$results);
		$this->assertEquals(404,$results->statusCode());
	}
/**
 * @test display method
 * @see 
 */
	public function testDisplay_Recode_NotFound(){
		require_once CakePlugin::path('ImageTable').'Config'.DS.'routes.php';
		$this->Controller->startupProcess();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$results = $this->Controller->display('100',200,100,'test.jpg');
		$this->assertInstanceOf('CakeResponse',$results);
		$this->assertEquals(404,$results->statusCode());
	}
/**
 * @test display method
 * @see 
 */
	public function testDisplay_File_MisssMatch_NotFound(){
		$this->_set_file();
		require_once CakePlugin::path('ImageTable').'Config'.DS.'routes.php';
		$this->Controller->startupProcess();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$results = $this->Controller->display('1',200,100,'testtest.jpg');
		$this->assertInstanceOf('CakeResponse',$results);
		$this->assertEquals(404,$results->statusCode());

		$this->_unset_file();
	}
/**
 * @test display method
 * @see 
 */
	public function testDisplay_Success(){
		$this->_set_file();
		require_once CakePlugin::path('ImageTable').'Config'.DS.'routes.php';
		$this->Controller->startupProcess();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$results = $this->Controller->display('1',200,100,'test.jpg');
		$this->assertEquals(200,$this->Controller->response->statusCode());
		$this->assertTrue(file_exists(TMP.'tests/1/200/100/test.jpg'));
		$this->_unset_file();
	}
}

