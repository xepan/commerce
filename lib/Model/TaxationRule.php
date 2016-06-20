<?php

 namespace xepan\commerce;

 class Model_TaxationRule extends \xepan\base\Model_Table{
 	public $table="taxation_rule";
 	public $actions = ['*'=>['view','edit','delete']];
	
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('priority')->type('Number')->defaultValue(0);

		$this->addField('type');
		$this->addField('created_by_id')->system(true)->defaultValue($this->app->employee->id);
		$this->addCondition('type','TaxationRule');

		$this->hasMany('xepan\commerce\TaxationRuleRow','taxation_rule_id');
		$this->hasMany('xepan\commerce\Item_Taxation_Association','taxation_rule_id');
		
		$this->is(['name|required']);
	}
}