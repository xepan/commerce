<?php
namespace xepan\commerce;
class Model_Store_Warehouse extends \xepan\base\Model_Contact{
	// public $table="store_warehouse";
	public $acl=false;

	function init(){
		parent::init();

		$this->addCondition('type','Warehouse');
		$this->hasMany('xepan\commerce\Store_StockTransaction','warehouse_id',null,'StoreTransactions');
	}
}