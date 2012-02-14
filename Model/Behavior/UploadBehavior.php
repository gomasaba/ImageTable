<?php
App::uses('Image', 'ImageTable.Model');

class UploadBehavior extends ModelBehavior {

	public $config;

	public $imagin;

	public function setup(Model $model, $config = array()) {
		$this->config = $config;
	}


	public function beforeValidate(Model $model) {
		if(!is_uploaded_file($model->data[$model->alias]['file']['tmp_name'])){
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
		system("rm -rf {$dir}");
	}
/**
 *  after delete
 *
 */
	public function prepare(Model $model){
		if(is_uploaded_file($model->data[$model->alias]['file']['tmp_name'])){
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
		$dir = (Configure::read('ImageTable.upload_base')) ? Configure::read('ImageTable.upload_base') : WWW_ROOT;
		$dir = rtrim($dir,DS).DS.$model->id;
		if(!is_dir($dir)){
			mkdir($dir,0777,true);
		}
		return $dir;
	}
/**
 *  after delete
 *
 */
	public function getImagine(){
		if(isset($this->imagin)){
			return $this->imagin;
		}
		$imaginbase = (Configure::read('ImageTable.Imagine_base')) ? Configure::read('ImageTable.Imagine_base') : VENDORS;
 		require_once 'phar://' . rtrim($imaginbase,DS). DS . 'imagine.phar';
 		return $this->imagin = new \Imagine\Gd\Imagine();
 	}

/**
 * 
 *
 * @var
 * @return bool
 */
	public function process(Model $model){
		$path = $this->getPath($model);
		if(is_uploaded_file($model->data[$model->alias]['file']['tmp_name'])){
			$img = $model->data[$model->alias];
			if(move_uploaded_file($img['file']['tmp_name'], $path.DS.$img['filename'])){
				if(array_key_exists('thumbnail',$this->config)){
					$this->createThumbnail($model);
				}
				return true;
			}else{
				$model->delete();
				return false;
			}
			// $model->data[$model->alias]['filename'] = $model->data[$model->alias]['file']['name'];
			// $model->data[$model->alias]['contents'] = file_get_contents($model->data[$model->alias]['file']['tmp_name']);
			// return true;
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
 		$Imagine =  $this->getImagine();
 		foreach($this->config['thumbnail'] as $prefix => $option){
 			$orign_fullpath = $this->getPath($model) .DS . $model->data[$model->alias]['filename'];
			$thumb_fullpath = $this->getPath($model) .DS . $prefix .'_'. $model->data[$model->alias]['filename'];

			$size = new Imagine\Image\Box($option['w'], $option['h']);
			if ($option['crop']) {
				$mode =  Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
			} else {
				$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
			}
			$Image = $Imagine->open($orign_fullpath);
			$createfile = $Image->thumbnail($size,$mode);
			$createfile->save($thumb_fullpath);
 		}
 	}

}