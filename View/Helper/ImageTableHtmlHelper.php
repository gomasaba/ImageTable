<?php
App::uses('AppHelper', 'View/Helper');

class ImageTableHtmlHelper extends AppHelper {

	public $default = array(
		'w'=>200,
		'h'=>100,
		'alt'=>null,
		'prefix'=>null,
	);

	public function image(array $record,$option=array()){
		if(array_key_exists('id',$record) && array_key_exists('filename',$record) && !empty($record['id']) && !empty($record['filename'])){
			$fullpath = $this->getPath($record);
			$url = (Configure::read('ImageTable.upload_url')) ? Configure::read('ImageTable.upload_url') : Router::url('/',true);
			if(!empty($option)){
				extract(array_replace_recursive($this->default,$option));
				if($prefix){
					$path = DS.$record['id'].DS.$prefix.$record['filename'];
				}else{
					$path = DS.$record['id'].DS.$w.DS.$h.DS.$record['filename'];
				}
			}else{
				$path = DS.$record['id'].DS.$record['filename'];			
			}
			$alttag = (isset($alt)) ? ' alt="'.$alt.'"':null;
			$tag = '<img src="'.$url.$path.'"'.$alttag.' />';
			return $tag;
		}
		return;
	}

	public function getPath(array $record){
		$dir = (Configure::read('ImageTable.upload_base')) ? Configure::read('ImageTable.upload_base') : WWW_ROOT;
		$dir = rtrim($dir,DS).DS.$record{'id'};
		return $dir;		
	}
}
