<?php

namespace xepan\commerce;

class Model_Store_TransactionRow extends \xepan\base\Model_Table{

	public $table="store_transaction_row";
	public $acl=false;

	function init(){
		parent::init();

		// $this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\commerce\Model_Store_TransactionAbstract','store_transaction_id');
		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
		$this->hasOne('xepan\commerce\Item','item_id');
		// $this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_generic_id');
		// $this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		$this->hasOne('xepan\production\Jobcard_Detail','jobcard_detail_id');

		$this->addField('quantity')->type('Number');
		$this->addField('status')->enum(['ToReceived','Received','Shipped','Delivered','Return']); // Shipped/Delivered used with transacion_type deliver
		$this->addField('extra_info');

		// $this->addExpression('item_id')->set($this->refSQL('qsp_detail_id')->fieldQuery('item_id'));
		$this->addExpression('type')->set($this->refSQL('store_transaction_id')->fieldQuery('type'));
		$this->addExpression('item_name')->set($this->refSQL('item_id')->fieldQuery('name'));
		
		$this->addExpression('related_sale_order')->set($this->refSQL('store_transaction_id')->fieldQuery('related_document_id'));
		$this->addExpression('from_warehouse')->set($this->refSQL('store_transaction_id')->fieldQuery('to_warehouse'));
	}

	function item(){
		return $this->ref('item_id');
	}
	

	function receive(){
		if(!$this->loaded())
			throw new \Exception("model transaction row must loaded", 1);
			
		$this['status'] = "Received";
		$this->save();
		
		return $this;
	}
}