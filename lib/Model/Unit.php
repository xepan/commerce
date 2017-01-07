<?php

 namespace xepan\commerce;

 class Model_Unit extends \xepan\base\Model_Table{
 	public $table="unit";
 	public $actions = ['*'=>['view','edit','delete']];
 	public $acl_type = "Unit";
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_UnitGroup','unit_group_id');
		$this->addField('name');
		
		$this->hasMany('xepan\commerce\UnitConversion','one_of_id');
		$this->hasMany('xepan\commerce\UnitConversion','to_become_id');
		
		$this->addExpression('name_with_group')->set(function($m,$q){
			return $q->expr('CONCAT([0]," - ",[1])',[$this->getElement('name'),$this->getElement('unit_group')]);
		});

		$this->is([
				'unit_group_id|required',
				'name|to_trim|required'
			]);
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){

		$old = $this->add('xepan\commerce\Model_Unit')
					->addCondition('name',$this['name'])
					->addCondition('unit_group_id',$this['unit_group_id'])
					->addCondition('id','<>',$this['id'])
				;
		$old->tryLoadany();
		if($old->loaded()){
			throw $this->exception('unit exist','ValidityCheck')->setField('name');
		}

	}

}
 
    