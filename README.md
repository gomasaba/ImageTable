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
		echo $this->Form->input('MainPhoto.file',array('type' => 'file'));
		echo $this->Form->input('MainPhoto.group',array('type' => 'hidden','value' => 'main'));
		echo $this->Form->input('MainPhoto.model',array('type' => 'hidden','value' => 'Post'));
	?>

	or

	<?php
		echo $this->ImageTableHtml->autoform();
	?>

View/Post/add.ctp
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
	


bootstrap.php
-----
example
	
	CakePlugin::load('ImageTable',array('routes'=>true));
	Configure::write('ImageTable.upload_url','http://'.getenv('SERVER_NAME').DS.'media');
	Configure::write('ImageTable.upload_base',getenv('DOCUMENT_ROOT').DS.'media');
	Configure::write('ImageTable.Imagine_base',realpath(getenv('DOCUMENT_ROOT'). '/../lib/Imagine'));

	or

	git submodule update --init


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

	fisrttime create thumbnail.
	secondtime access generate file.
		


