<?php 

 namespace xepan\commerce;

 class Model_Item_Taxation_Association extends \xepan\base\Model_Table{
 	public $acl =false;
	public $table = "taxation_association";
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');

		$this->hasOne('xepan\commerce\TaxationRule','taxation_rule_id');

		// //tax percent
		$this->addExpression('priority')->set(function($m){
	        return $m->refSQL('taxation_rule_id')->fieldQuery('priority');
	    });

		
	}
} 
 
	

