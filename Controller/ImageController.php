<?php
App::uses('Image', 'ImageTable.Model');
class ImageController extends AppController {

/*
 * Dinamic display image
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

		$path = $this->Image->Behaviors->Upload->getPath($this->Image);

		$orign_fullpath = $path .DS . $filename;
		$thumb_basepath = $path .DS . $width . DS .$height;
		$thumb_fullpath = $thumb_basepath . DS . $filename;

		if(!is_dir($thumb_basepath)){
			mkdir($thumb_basepath,0777,true);
		}

		if(!file_exists($thumb_fullpath)){
			$Imagine = $this->Image->Behaviors->Upload->getImagine();
			$size = new Imagine\Image\Box($width, $height);
			$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;

			$Image = $Imagine->open($orign_fullpath);
			$createfile = $Image->thumbnail($size,$mode);
			$createfile->save($thumb_fullpath);
		}
		// output
		$this->viewClass = 'Media';
		$params = array(
						'id' => $filename,
						'name' => $filename,
						'download' => false,
						'extension' => $image['Image']['type'],
						'path' => $thumb_basepath.DS
					);
		$this->set($params);
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

