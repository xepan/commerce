<?php

 namespace xepan\commerce;

 class Model_ShippingRuleRow extends \xepan\base\Model_Table{
 	public $table="shipping_rule_row";
 	public $actions = ['*'=>['view','edit','delete']];
	
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\ShippingRule','shipping_rule_id');

		$this->addField('from');
		$this->addField('to');
		$this->addField('shipping_charge');
		$this->addField('shipping_duration')->type('text');
		$this->addField('shipping_duration_days')->type('number');
		$this->addField('express_shipping_charge');
		$this->addField('express_shipping_duration')->type('text');
		$this->addField('express_shipping_duration_days')->type('number');

		$this->addField('type');
		$this->addField('created_by_id')->system(true)->defaultValue($this->app->employee->id);
		$this->addCondition('type','ShippingRuleRow');
		
		$this->is([
					'shipping_charge|number|required',
					'from|required',
					'to|required'
				]);
	}
}