<?php
App::uses('AppHelper', 'View/Helper');

class ImageTableHtmlHelper extends AppHelper {

	public $helpers = array('Form','Html');

	public $hasManyCount = 3;


	public $hasOne;

	public $hasMany;


	public $parseAssociation = array(
	);


	public function autoform($model=null,$attribute=null){
		if (is_array($model) && empty($attribute)) {
			$attribute = $model;
			$model = null;
		}
		if (empty($model) && $model !== false && !empty($this->request->params['models'])) {
			$model = key($this->request->params['models']);
		}
		$modelObj = ClassRegistry::getObject($model);

		$this->hasOne = (isset($modelObj->hasOne)) ? $this->prepare($modelObj->hasOne) : false;
		$this->hasMany = (isset($modelObj->hasMany)) ? $this->prepare($modelObj->hasMany) : false;

		$out = array();

		if($this->hasOne){
			foreach ($this->hasOne as $key => $prepared) {
				if(isset($this->request->data[$prepared['className']])){
					$out[] = $this->__editfile($this->request->data[$prepared['className']],$attribute);
				}else{
					$out[] = $this->__inputfile($prepared);						
				}
			}
		}
		if($this->hasMany){
			foreach ($this->hasMany as $key => $prepared) {
				if(isset($this->request->data[$prepared['className']])){
					$this->hasManyCount = $this->hasManyCount - count($this->request->data[$prepared['className']]);
					foreach($this->request->data[$prepared['className']] as $record){
						$out[] = $this->__editfile($record,$attribute);
					}
				}
				for($i=1; $i <= $this->hasManyCount; $i++){
					$out[] = $this->__inputfile($prepared,$i);					
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

	protected function __inputfile($prepared,$multi=false){
		$out = '';
		$key = ($multi) ? $prepared['className'].'.'.$multi : $prepared['className'];
		$out .= $this->Form->input($key.'.file',array('type'=>'file'));
		$out .= $this->Form->input($key.'.model',array('type'=>'hidden','value'=>$prepared['model']));
		$out .= $this->Form->input($key.'.groupname',array('type'=>'hidden','value'=>$prepared['groupname']));			
		return $out;
	}

	protected function __editfile($data,$attribute=array()){
		$out = '';
		$out .= $this->image($data,$attribute);
		$out .= $this->Html->link(__('delete'),array('controller'=>'image','action'=>'delete',$data['id'],'plugin'=>'ImageTable'));
		return $out;
	}


	public function image(array $record,$option=array()){
		if(array_key_exists('id',$record) && array_key_exists('filename',$record) && !empty($record['id']) && !empty($record['filename'])){
			$fullpath = $this->getPath($record);
			$url = (Configure::read('ImageTable.upload_url')) ? Configure::read('ImageTable.upload_url') : Router::url('/',true);			
			if(isset($option['prefix'])){
				$path = DS.$record['id'].DS.$option['prefix'].'_'.$record['filename'];
				unset($option['prefix']);
			}
			if(isset($option['w']) && isset($option['h'])){
				$path = DS.$record['id'].DS.$option['w'].DS.$option['h'].DS.$record['filename'];
				unset($option['w'],$option['h']);
			}
			if(!isset($path)){				
				$path = DS.$record['id'].DS.$record['filename'];
			}
			return $this->Html->image($url.$path,$option);
		}
		return;
	}

	public function getPath(array $record){
		$dir = (Configure::read('ImageTable.upload_base')) ? Configure::read('ImageTable.upload_base') : WWW_ROOT;
		$dir = rtrim($dir,DS).DS.$record{'id'};
		return $dir;		
	}
}
