<?php

Class Image extends AppModel{


	public $actsAs = array(
		'ImageTable.Upload'=>array(
				'thumbnail'=>array(
					'thumb_s' => array(
						'w' => 100,
						'h' => 75,
						'crop' => false,
					),
					'thumb_m' => array(
						'w' => 300,
						'h' => 200,
						'crop' => false,
					),
				)
		),
	);
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'model' => array(
			'rule' => 'notEmpty',
			'message' => 'Model Name not empty',
			'on' => 'create',
		),
		'file' => array(
			'name' => array(
					'rule' => array('checkFilename'),
					'on' => 'create',
					'message' => 'filename is not alphanumeric',
					'allowEmpty'=>true,
					'required'=>false,
			),
			'type'=>array(
					'rule' => array('extension'),
					'on' => 'create',
					'message' => 'file is not allow extension',
					'allowEmpty'=>true,
					'required'=>false,
			),
			'size' => array(
					'rule' => array('checkSize', 2097152),
					'on' => 'create',
					'message' => 'filesize too large limit is 2MB',
					'allowEmpty'=>true,
					'required'=>false,
			),
		),
	);

/**
 * Validation checkFilename
 *
 * @return boolean
 */
	public function checkFilename($file) {
		if(!empty($file['file']['name'])){
			if (preg_match("/^(^[a-zA-Z0-9\-\.\_]+)\.([a-zA-Z]+)$/", $file['file']['name'])) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
/**
 * Validation checkSize
 *
 * @return boolean
 */
	public function checkSize($file, $max = false) {
		if(!empty($file['file']['size'])){
			if($file['file']['size'] > $max){
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}

}