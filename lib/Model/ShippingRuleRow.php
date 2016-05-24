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

		$this->addField('type');
		$this->addCondition('type','Shipping_Rule_Row');
		
		$this->is([
					'shipping_charge|number|required',
					'from|required',
					'to|required'
				]);
	}
}