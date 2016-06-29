<?php 

 namespace xepan\commerce;

 class Model_Item_Shipping_Association extends \xepan\base\Model_Table{
 	public $acl =false;
	public $table = "shipping_association";
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\commerce\ShippingRule','shipping_rule_id');
		
		$this->addField('priority')->type('Number')->defaultValue(0);
		$this->addExpression('based_on')->set($this->refSQL('shipping_rule_id')->fieldQuery('based_on'));
		$this->addExpression('country')->set($this->refSQL('shipping_rule_id')->fieldQuery('country'));
		$this->addExpression('state')->set($this->refSQL('shipping_rule_id')->fieldQuery('state'));
		
		$this->setOrder('priority','desc');		
	}
} 
 
	

