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

		//tax percent
		$this->addExpression('tax_percent')->set(function($m){
	        return $m->refSQL('taxation_id')->fieldQuery('percentage');
	    });

		
	}
} 
 
	

