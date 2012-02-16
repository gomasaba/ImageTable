<?php
App::uses('AppHelper', 'View/Helper');

class ImageTableHtmlHelper extends AppHelper {

	public $helpers = array('Form','Html');

	public $hasManyCount = 3;


	public $hasOne;

	public $hasMany;



	public $default = array(
		'w'=>200,
		'h'=>100,
		'alt'=>null,
		'prefix'=>null,
	);

	public $parseAssociation = array(
	);


	public function autoform($model=null,$attribute=null){
		if (is_array($model) && empty($attribute)) {
			$options = $model;
			$model = null;
		}
		if (empty($model) && $model !== false && !empty($this->request->params['models'])) {
			$model = key($this->request->params['models']);
		}
		$modelObj = ClassRegistry::getObject($model);

		$this->hasOne = (isset($model->hasOne)) ? $this->prepare($model->hasOne) : false;
		$this->hasMany = (isset($model->hasMany)) ? $this->prepare($model->hasMany) : false;

		$out = array();

		if($this->hasOne){
			foreach ($this->hasOne as $row) {
				$out[] = $this->__inputfile($key,$row['model'],$row['groupname'],$attribute);
			}
		}


		foreach ($this->association as $type => $val) {
			if($type==='hasMany'){
				foreach ($val as $key=>$row) {
					$mkey = $row['classAlias'].'.'.$key;					
					$out[] = $this->__inputfile($mkey,$row['model'],$row['groupname'],$attribute);					
				}
			}else{
				foreach ($val as $row) {
					$key = $row['classAlias'];
					if(isset($this->request->data[$key]) && !empty($this->request->data[$key]['id'])){
						$out[] = $this->__editfile($this->request->data[$key],$attribute);						
					}else{
						$out[] = $this->__inputfile($key,$row['model'],$row['groupname'],$attribute);
					}
				}
			}
		}
		if(count($out)>0){
			return implode("\n",$out);
		}
	}

	public function prepare($assoc){
		$search = function($conditions,$type){
			foreach ($conditions as $key => $value) {
				if(preg_match("/$type/", $key)){
					return $value;
				}
			}
		};
		$return = array();
		foreach ($assoc as $className => $value) {
			$maping['className'] = $className;
			$maping['model'] =  $search($value['conditions'],'model');
			$maping['groupname'] = $search($value['conditions'],'groupname');
			array_push($return,$maping);
		}
		return $return;
	}

	protected function __inputfile(){
		$out = '';
		$out .= $this->Form->input(rtrim($key,'.').'.file',array('type'=>'file'));
		$out .= $this->Form->input(rtrim($key,'.').'.model',array('type'=>'hidden','value'=>$model));
		$out .= $this->Form->input(rtrim($key,'.').'.groupname',array('type'=>'hidden','value'=>$groupname));
		return $out;
	}

	protected function __editfile($data,$attribute){
		$out = '';
		$out .= $this->image($data,$attribute);
		$out .= $this->Html->link('delete',array('controller'=>'image','action'=>'delete',$data['id'],'plugin'=>'ImageTable'));
		return $out;
	}


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
