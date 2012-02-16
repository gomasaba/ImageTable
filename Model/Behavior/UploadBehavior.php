<?php
App::uses('Image', 'ImageTable.Model');

class UploadBehavior extends ModelBehavior {

	public $config;

	public $imagine;


	public function setup(Model $model, $config = array()) {
		//imagie check
		$imaginebase = (Configure::read('ImageTable.Imagine_base')) ? Configure::read('ImageTable.Imagine_base') : VENDORS;
		if(!file_exists(rtrim($imaginebase,DS) . DS . 'imagine.phar')){
			$imaginebase = CakePlugin::path('ImageTable') . 'Vendor' . DS . 'Imagine';
			if(!file_exists( $imaginebase . DS . 'imagine.phar')){
				throw new CakeException('imagine.phar not found.');
			}
		}
		require_once 'phar://' . rtrim($imaginebase,DS). DS . 'imagine.phar';
 		$this->imagine = new \Imagine\Gd\Imagine();
		$this->config = $config;
	}


	public function beforeValidate(Model $model) {
		if(!$this->is_uploaded_file($model->data[$model->alias]['file']['tmp_name'])){
			unset($model->data[$model->alias]);
		}
		if(empty($model->data[$model->alias]['file']['tmp_name'])){
			unset($model->data[$model->alias]);
		}		
		return true;
	}

/**
 *  Before Save
 *
 */
	public function beforeSave(Model $model) {
		if(isset($model->data[$model->alias]['file']['tmp_name'])){
			$this->prepare($model);
		}
		return true;
	}
/**
 *  after Save
 *
 */
	public function afterSave(Model $model,$created) {
		$this->process($model);
	}
/**
 *  after delete
 *
 */
	public function afterDelete(Model $model) {
		$dir = $this->getPath($model);
		$iterator = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
			if ($file->isDir()) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
	}

	public function move_uploaded_file($filename, $destination){
		return move_uploaded_file($filename, $destination);
	}

	public function is_uploaded_file($filename){
		return is_uploaded_file($filename);
	}
/**
 *  $model->data[$model->alias] prepare for save method
 *
 */
	public function prepare(Model $model){
		if($this->is_uploaded_file($model->data[$model->alias]['file']['tmp_name'])){
			$filename = $model->data[$model->alias]['file']['name'];
			$model->data[$model->alias]['filename'] = $model->data[$model->alias]['file']['name'];
			$model->data[$model->alias]['type'] = substr($filename, strrpos($filename, '.') + 1);
		}
	}
/**
 *  after delete
 *
 */
	public function getPath(Model $model){
		if($model->id !=null){
			$dir = (Configure::read('ImageTable.upload_base')) ? Configure::read('ImageTable.upload_base') : WWW_ROOT;
			$dir = rtrim($dir,DS).DS.$model->id;
			if(!is_dir($dir)){
				mkdir($dir,0777,true);
			}
			return $dir;		
		}else{
			return false;
		}
	}
/**
 * upload file and create thumbnails.
 *
 * @var
 * @return bool
 */
	public function process(Model $model){
		$path = $this->getPath($model);
		if($this->is_uploaded_file($model->data[$model->alias]['file']['tmp_name'])){
			$img = $model->data[$model->alias];
			if($this->move_uploaded_file($img['file']['tmp_name'], $path.DS.$img['filename'])){
				if(array_key_exists('thumbnail',$this->config)){
					$this->createThumbnail($model);
				}
				return true;
			}else{
				$model->delete();
				return false;
			}
		}else{
			return false;
		}
	}	
/**
 * 
 *
 * @var
 * @return bool
 */
 	public function createThumbnail(Model $model){
 		foreach($this->config['thumbnail'] as $prefix => $option){
 			$orign_fullpath = $this->getPath($model) .DS . $model->data[$model->alias]['filename'];
			$thumb_fullpath = $this->getPath($model) .DS . $prefix .'_'. $model->data[$model->alias]['filename'];

			$size = new Imagine\Image\Box($option['w'], $option['h']);
			if ($option['crop']) {
				$mode =  Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
			} else {
				$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
			}
			$Image = $this->imagine->open($orign_fullpath);
			$createfile = $Image->thumbnail($size,$mode);
			$createfile->save($thumb_fullpath);
 		}
 	}

}