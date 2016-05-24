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
		
	}
} 
 
	

