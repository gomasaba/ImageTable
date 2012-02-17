Simple upload Image and Generate Thumbnail for CakePHP2
=======================================================

Thumbnail Generate from Imagine

[Imagine] (https://github.com/avalanche123/Imagine)

I inspire from MediaPlugin and Attach

	MediaPlugin https://github.com/davidpersson/media
	Attach  https://github.com/krolow/Attach


create images table
-----
	ex).
	CREATE TABLE  `images` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `model` varchar(150) NOT NULL,
	  `foreign_key` int(11) NOT NULL,
	  `groupname` varchar(64),
	  `filename` varchar(150) NOT NULL,
	  `type` varchar(32) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8

	or

	cake schema create --plugin=ImageTable


bootstrap.php
-----
custom example
	
	CakePlugin::load('ImageTable',array('routes'=>true)); // ROOT/image/ routing active
	Configure::write('ImageTable.upload_url','http://'.getenv('SERVER_NAME').DS.'media'); // default IMAGES_URL
	Configure::write('ImageTable.upload_base',getenv('DOCUMENT_ROOT').DS.'media'); //default IMAGES
	Configure::write('ImageTable.Imagine_base',realpath(getenv('DOCUMENT_ROOT'). '/../Imagine')); // another path Imagine


Imagine submodule in Vendor

	git submodule update --init

Model/Post.php
-----
example

	public $hasOne = array(
		'MainPhoto' => array(
			'className' => 'ImageTable.Image',
			'foreignKey' => 'foreign_key',
			'conditions' => array('MainPhoto.model' => 'Post', 'MainPhoto.group' => 'main'),
			'dependent' => true,
		),
	);

	public $hasMany = array(
		'Photo' => array(
			'className' => 'ImageTable.Image',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Photo.model' => 'Post'),
			'dependent' => true,
		),
	);

View/Post/add.ctp
-----
	<?php echo $this->Form->create('Post',array('type'=>'file'));?>
	<?php
		echo $this->ImageTableHtml->autoform();
	?>

example custom HTML tag use.

	<?php
		$this->ImageTableHtml->autoRenderString = false;
		echo $this->Html->nestedList(
			$this->ImageTableHtml->autoform(array('label'=>false))
			,array('class'=>'inputThumb')
		);
	?>


View/Post/edit.ctp
-----
example

	<?php
		echo $this->ImageTableHtml->autoform(array('prefix'=>'thumb_s'));
	?>


Controller/PostController.ctp
-----
	<?php
		$this->Post->saveAll($this->request->data,array('validate'=>'first'));
	?>

	Post save success beforeSave run upload files..


delete file
-----
	
	$this->Html->link('delete',array('controller'=>'image','action'=>'delete',$id,'plugin'=>'ImageTable'));
	


Dynamic create thumbnail
-----
example

upload_base = /www/html/upload/

	setup .htaccess

		RewriteEngine on
		RewriteCond %{REQUEST_FILENAME} upload/(.*)$
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^upload/(.*)$ http://localhost/image/$1 [L]
	
		
	<img src="http://localhost/upload/1/100/75/test.jpg" />

	fisrt time create thumbnail.
	second time access generate file.
		


