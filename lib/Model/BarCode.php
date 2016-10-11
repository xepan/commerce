<?php

namespace xepan\commerce;

class Model_BarCode extends \xepan\base\Model_Table{
	public $table='dispatch_barcode';
	public $acl = false;
	// public $status = ['Active','DeActive','Used'];
	// public $actions = [
	// 				'Active'=>['view','edit','delete','deactivate','used'],
	// 				'DeActive'=>['view','edit','delete','activate']
	// 			];

	function init(){
		parent::init();

		$this->addField('name')->caption('BarCode Number');
		$this->addField('is_used')->type('boolean');

		$this->addField('related_document_id');
		$this->addField('related_document_type');

		// $this->addField('status')->enum(['Active','DeActive'])->defaultValue('Active');
		// $this->addCondition('type','BarCode');
		$this->is([
				'name|required|unique'
				]);
	}

	
	function markBarCodeUsed($related_document_id,$related_document_type){
		if(!$this->loaded()){
			throw new \Exception("BarCode Not Loaded", 1);
		}

		$m = $this->add('xepan\commerce\Model_BarCode');
		$m->addCondition('name',$this['name']);
		$m->setLimit(1);
		$m->tryLoadAny();
			$m['is_used']=1;
			$m['related_document_id']=$related_document_id;
			$m['related_document_type']=$related_document_type;
			// $m['status']="Used";
			$m->save();
	}
}

