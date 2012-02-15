<?php
App::uses('Image', 'ImageTable.Model');
class ImageController extends AppController {

	public $uses = array('ImageTable.Image');

/**
 * Dynamic display image
 * 
 * 
 * 
 */
	public function display($id,$width,$height,$filename){
		$this->Image->id = $id;
		if (!$this->Image->exists()) {
			return new CakeResponse(array('body' =>'not found','status'=>404));
		}
		$image = $this->Image->read(null, $id);
		if($image['Image']['filename'] != $filename){
			return new CakeResponse(array('body' =>'not found','status'=>404));			
		}
		if(!$this->Image->Behaviors->Upload){
			$this->Image->Behaviors->attach('Upload');
		}
		$path = $this->Image->Behaviors->Upload->getPath($this->Image);

		$orign_fullpath = $path .DS . $filename;
		$thumb_basepath = $path .DS . $width . DS .$height;
		$thumb_fullpath = $thumb_basepath . DS . $filename;

		if(!is_dir($thumb_basepath)){
			mkdir($thumb_basepath,0777,true);
		}

		if(file_exists($thumb_fullpath)){
			return $this->__output($filename,$image['Image']['type'],$thumb_basepath.DS,$image['Image']['type']);
		}else{
			if($this->__createThumbnail($orign_fullpath,$thumb_fullpath,$width,$height)){
				return $this->__output($filename,$image['Image']['type'],$thumb_basepath.DS,$image['Image']['type']);
			}		
		}
		return new CakeResponse(array('body' =>'not found','status'=>404));
	}
/**
 * Output Image
 * 
 * @return void
 * 
 */
	public function __output($filename,$extension,$path,$extension){
		// output
		$this->viewClass = 'Media';
		$params = array(
						'id' => $filename,
						'name' => $filename,
						'download' => false,
						'extension' => $extension,
						'path' => $path
					);
		$this->set($params);
	}
/**
 * Generate Thumbnail
 * 
 * @return void false NotFound Response
 * 
 */

	public function __createThumbnail($fille,$to,$width,$height){
		try{
			$Imagine = $this->Image->Behaviors->Upload->imagine;
			$size = new Imagine\Image\Box($width, $height);
			$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
			$Image = $Imagine->open($fille);
			$createfile = $Image->thumbnail($size,$mode);
			$createfile->save($to);
			return true;
		}catch(Eexception $e){
			return new CakeResponse(array('body' =>'not found','status'=>404));
		}
		
	}
/*
 * Delete Image
 * 
 * 
 * 
 */
	public function delete($id){
		$this->Image->id = $id;
		if (!$this->Image->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Image')));
		}
		if ($this->Image->delete()) {
			$this->Session->setFlash( __('The %s deleted Success', __('Image')));
		}else{
			$this->Session->setFlash( __('The %s deleted Failed', __('Image')));			
		}
		$this->redirect($this->referer());
	}

}

