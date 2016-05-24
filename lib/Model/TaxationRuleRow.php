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

		$this->is([
					'name|required',
					'taxation_id|required',
					'taxation_rule_id|required',
					'country_id|required',
					'state_id|required'
				]);
	}
}