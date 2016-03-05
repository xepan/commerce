<?php

 namespace xepan\commerce;

 class Model_CategoryItemAssociation extends \xepan\base\Model_Table{
 	public $acl =false;
	public $table = 'category_item_association';
	public $status = [];

	public $actions = [
					'*'=>['view','edit','delete','deactivate']
					];
	
	function init(){
		parent::init();

		$this->hasOne('xepan/commerce/Item','item_id');
		$this->hasOne('xepan/commerce/Category','category_id');

		$this->addExpression('type')->set("'CategoryItemAssociation'");

	}
}
 
    