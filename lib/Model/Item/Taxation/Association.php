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
		$this->hasOne('xepan\commerce\Taxation','taxation_id');

		$this->addExpression('percentage')->set($this->refSQL('taxation_id')->fieldQuery('percentage'));
		
	}
} 
 
	

