<?php

 namespace xepan\commerce;

 class Model_UnitConversion extends \xepan\base\Model_Table{
 	public $table="unit_conversion";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_Unit','one_of_id');//->display(array('form'=>'xepan\base\DropDownNormal'));
		$this->addField('multiply_with');
		$this->hasOne('xepan\commerce\Model_Unit','to_become_id');//->display(array('form'=>'xepan\base\DropDownNormal'));
		
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
	}

}
 
    