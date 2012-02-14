Simple upload Image and Generate Thumbnail for CakePHP2
=======================================================

Thumbnail Generate from Imagine

[Imagine](https://github.com/avalanche123/Imagine)


create images table
-----
	ex).
	CREATE TABLE  `images` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `model` varchar(150) NOT NULL,
	  `foreign_key` int(11) NOT NULL,
	  `groupname` varchar(64) NOT NULL,
	  `filename` varchar(150) NOT NULL,
	  `type` varchar(32) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8

	or

	cake schema create --plugin=ImageTable


Model/Post.php
-----
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


Controller/PostController.ctp
-----
	<?php
		$this->Post->saveAll($this->request->data,array('validate'=>'first'));
	?>

	Post save success beforeSave run upload files..
	


bootstrap.php
-----
	
	// enable Plugin rotes.
	CakePlugin::load('ImageTable',array('routes'=>true));

		example
		image/:id/:width/:height/:filenmae.jpg -> image/1/100/75/test.jpg
		image/delete/:id  -> image/delete/1

	// upload base URL  default getenv('SERVE_NAME')
	Configure::write('ImageTable.upload_url','http://'.getenv('SERVER_NAME').DS.'media');

	// upload directory default WWW_ROOT
	Configure::write('ImageTable.upload_base',getenv('DOCUMENT_ROOT').DS.'media');

	// Imagine Library default Vendors
	Configure::write('ImageTable.Imagine_base',realpath(getenv('DOCUMENT_ROOT'). '/../lib/Imagine'));
		or
	git submodule update --init


delete file?
-----
	
	$this->Html->link('delete',array('controller'=>'image','action'=>'delete',$id,'plugin'=>'ImageTable'));
	

save path
-----
	/{ImageTable.upload_base} or { WWW_ROOT} / images primary_key / upload_filename.ext


Uses ImageTableHtmlHelper
-----
	
	Dinamic create thumbnail

	<?php echo $this->ImageTableHtml->image($post['MainPhoto']);?> ->originam image
	

	Dinamic Image uses.

	option.
	<?php echo $this->ImageTableHtml->image($post['MainPhoto'],arary('w'=>200,'h'=>200,'alt'=>'test'));?>
	<img src="http://example/cakepath/image/1/100/75/test.jpg" />

	example. upload_base = media and cake other dirctory...
	-----
		RewriteEngine on
		RewriteCond %{REQUEST_FILENAME} media/(.*)$
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^media/(.*)$ http://example/cakepath/image/$1 [L]


