<?php

 namespace xepan\commerce;

 class Model_TaxationRuleRow extends \xepan\base\Model_Table{
 	public $table="taxation_rule_row";
 	public $actions = ['*'=>['view','edit','delete']];
	
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Taxation','taxation_id');
		$this->hasOne('xepan\commerce\TaxationRule','taxation_rule_id');
		$this->hasOne('xepan\base\Country','country_id');
		$this->hasOne('xepan\base\State','state_id');

		$this->addField('name');
		$this->addField('type');
		$this->addCondition('type','Taxation_Rule_Row');
		$this->addField('created_by_id')->system(true)->defaultValue($this->app->employee->id);
		$this->addExpression('percentage')->set($this->refSQL('taxation_id')->fieldQuery('percentage'));

		$this->addExpression('priority')->set(function($m,$q){
			return $q->expr("IF( ([0] is NULL AND [1] is NULL ), 0, IF( ( [0] is NULL OR [1] is NULL ), 1, 2) )",[$m->refSQL('country_id')->fieldQuery('name'),$m->refSQL('state_id')->fieldQuery('name')]);
		});

		$this->is([
					'name|required',
					'taxation_id|required',
					'taxation_rule_id|required'
				]);
	}
}