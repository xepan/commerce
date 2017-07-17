<?php

 namespace xepan\commerce;

 class Model_PackageItemAssociation extends \xepan\base\Model_Table{
 	public $acl =false;
	public $table = 'commerce_package_item_association';
	public $status = [];

	public $actions = [
					'*'=>['view','edit','delete','deactivate']
					];
	
	function init(){
		parent::init();

		$this->hasOne('xepan/commerce/Item','package_item_id');
		$this->hasOne('xepan/commerce/Store_Item','item_id')->display(array('form'=>'xepan\commerce\Item'));
		$this->addField('qty');
		$this->addField('extra_info')->type('text');

		// $this->addExpression('type')->set("'PackageItemAssociation'");
	}
}
 
    