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

		$this->addExpression('category_display_sequence')->set($this->refSql('category_id')->fieldQuery('display_sequence'));
		$this->addExpression('is_template')->set($this->refSql('item_id')->fieldQuery('is_template'));
		$this->addExpression('sale_price')->set($this->refSql('item_id')->fieldQuery('sale_price'));

	}
}
 
    