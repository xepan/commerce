<?php

 namespace xepan\commerce;

 class Model_ShippingRule extends \xepan\base\Model_Table{

 	public $table="shipping_rule";
 	public $actions = ['*'=>['view','edit','delete']];
	
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Country','country_id');
		$this->hasOne('xepan\base\State','state_id');

		$this->addField('name');
		$this->addField('based_on')->setValueList(['amount'=>'Amount','quantity'=>"Quantity",'weight'=>"Weight"/*,'volume'=>"Volume"*/]);

		$this->addField('type');
		$this->addCondition('type','Shipping_Rule');
		
		$this->hasMany('xepan\commerce\ShippingRuleRow','shipping_rule_id');
		$this->hasMany('xepan\commerce\Item_Shipping_Association','shipping_rule_id');
		
		$this->is(['name|required']);

		
	}
}