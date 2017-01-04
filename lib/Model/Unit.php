<?php

 namespace xepan\commerce;

 class Model_Unit extends \xepan\base\Model_Table{
 	public $table="unit";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_UnitGroup','unit_group_id');
		$this->addField('name');
		
		$this->hasMany('xepan\commerce\UnitConversion','one_of_id');
		$this->hasMany('xepan\commerce\UnitConversion','to_become_id');
		
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
 
    