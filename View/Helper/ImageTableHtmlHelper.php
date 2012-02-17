<?php
App::uses('AppHelper', 'View/Helper');

class ImageTableHtmlHelper extends AppHelper {

	public $helpers = array('Form','Html');

	public $hasManyCount = 3;

	public $hasOne;

	public $hasMany;

	public $autoRenderString = true;

	public function autoform($model=null,$attribute=array()){
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
				if(isset($this->request->data[$prepared['className']]) && isset($this->request->data[$prepared['className']]['id'])){
					$data[$prepared['className']] = $this->request->data[$prepared['className']];
					$out[] = $this->__editfile($data,$attribute);
					unset($data);
				}else{
					$out[] = $this->__inputfile($prepared,$attribute);						
				}
			}
		}
		if($this->hasMany){
			foreach ($this->hasMany as $key => $prepared) {
				if(isset($this->request->data[$prepared['className']])){
					$count = 0;
					foreach($this->request->data[$prepared['className']] as $record){
						if(isset($record['id'])){
							$data[$prepared['className']] = $record;
							$out[] = $this->__editfile($data,$attribute,$record['id']);
							unset($data);
							$count++;
						}
					}
					$this->hasManyCount = $this->hasManyCount - $count;
					$next_id = Set::extract($this->request->data[$prepared['className']],'./id');
					sort($next_id);
					$next_id = array_pop($next_id);
					$next_id++;
				}
				if(isset($count) && $count > 0){
					$this->hasManyCount = $next_id + $this->hasManyCount;
				}
				if(!isset($next_id)) $next_id = 1;
				while($next_id < $this->hasManyCount){
					$out[] = $this->__inputfile($prepared,$attribute,$next_id);
					$next_id++;
				}
			}
		}
		if(count($out)>0){
			if($this->autoRenderString){
				return implode("\n",$out);				
			}
			return $out;
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

	protected function __inputfile($prepared,$attribute=array(),$multi=false){
		$out = '';
		$key = ($multi) ? $prepared['className'].'.'.$multi : $prepared['className'];
		$out .= $this->Form->input($key.'.file',array_merge(array('type'=>'file'),$attribute));
		$out .= $this->Form->input($key.'.model',array('type'=>'hidden','value'=>$prepared['model']));
		$out .= $this->Form->input($key.'.groupname',array('type'=>'hidden','value'=>$prepared['groupname']));
		return $out;
	}

	protected function __editfile($data,$attribute=array(),$multi=false){
		$out = '';
		$model = key($data);
		$key = ($multi) ? key($data).'.'.$multi : key($data);		
		$out .= $this->image($data[$model],$attribute);
		$out .= $this->Html->link(__('delete'),array('controller'=>'image','action'=>'delete',$data[$model]['id'],'plugin'=>'ImageTable'));
		$out .= $this->Form->hidden($key.'.id',array('value'=>$data[$model]['id']));
		$out .= $this->Form->hidden($key.'.model',array('value'=>$data[$model]['model']));
		$out .= $this->Form->hidden($key.'.filename',array('value'=>$data[$model]['filename']));
		return $out;
	}


	public function image(array $record,$option=array()){
		if(array_key_exists('id',$record) && array_key_exists('filename',$record) && !empty($record['id']) && !empty($record['filename'])){
			$fullpath = $this->getPath($record);
			$url = (Configure::read('ImageTable.upload_url')) ? Configure::read('ImageTable.upload_url') : DS.IMAGES_URL;	
			var_dump(IMAGES_URL);
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
		$dir = (Configure::read('ImageTable.upload_base')) ? Configure::read('ImageTable.upload_base') : IMAGES;
		$dir = rtrim($dir,DS).DS.$record{'id'};
		return $dir;		
	}
}
