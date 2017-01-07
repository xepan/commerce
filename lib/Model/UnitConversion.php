<?php

 namespace xepan\commerce;

 class Model_UnitConversion extends \xepan\base\Model_Table{
 	public $table="unit_conversion";
 	public $actions = ['*'=>['view','edit','delete']];
 	public $acl_type = "UnitConversion";
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_Unit','one_of_id','name_with_group');//->display(array('form'=>'xepan\base\DropDownNormal'));
		$this->addField('multiply_with');
		$this->hasOne('xepan\commerce\Model_Unit','to_become_id','name_with_group');//->display(array('form'=>'xepan\base\DropDownNormal'));
		
		$this->addExpression('one_of_unit_group_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('one_of_id')->fieldQuery('unit_group_id')]);
		});

		$this->addExpression('to_become_unit_group_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('to_become_id')->fieldQuery('unit_group_id')]);
		});

		$this->is([
			'one_of_id|required',
			'to_become_id|required',
			'multiply_with|to_trim|required|gt|0'
			]);

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$one_of_unit = $this->add('xepan\commerce\Model_Unit')->load($this['one_of_id']);
		$to_become_unit = $this->add('xepan\commerce\Model_Unit')->load($this['to_become_id']);

		if($one_of_unit['unit_group_id'] != $to_become_unit['unit_group_id'])
			throw $this->exception('unit must belong with one unit group','ValidityCheck')->setField('one_of_id');

		$old_uc_model = $this->add('xepan\commerce\Model_UnitConversion');
		$old_uc_model->addCondition('to_become_id',$this['to_become_id']);
		$old_uc_model->addCondition('one_of_id',$this['one_of_id']);
		$old_uc_model->addCondition('one_of_unit_group_id',$this['one_of_unit_group_id']);
		$old_uc_model->addCondition('to_become_unit_group_id',$this['to_become_unit_group_id']);
		$old_uc_model->addCondition('id','<>',$this->id);
		$old_uc_model->tryLoadAny();
		if($old_uc_model->loaded())
			throw $this->exception('conversion entry already exist','ValidityCheck')->setField('one_of_id');
		
	}

	function isConversionExist($item_unit_id,$qsp_unit_id,$item_unit_group_id){

		if($item_unit_id == $qsp_unit_id) return true;

		$uc_model = $this->add('xepan\commerce\Model_UnitConversion');
		$uc_model->addCondition('to_become_id',$item_unit_id);
		$uc_model->addCondition('one_of_id',$qsp_unit_id);
		$uc_model->addCondition('one_of_unit_group_id',$item_unit_group_id);
		$uc_model->addCondition('to_become_unit_group_id',$item_unit_group_id);		
		
		return $uc_model->count()->getOne();
	}

}
 
    