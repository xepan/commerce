<?php

namespace xepan\commerce;

class Model_Store_Transaction extends \xepan\base\Model_Table{

	public $table="store_transaction";
	public $acl=false;

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','from_warehouse_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','to_warehouse_id');
		
		$this->addField('related_document_id'); //Sale Ordre/Purchase
		$this->addField('document_type'); //Purchase/Sale/Dispatch

		$this->hasMany('xepan\commerce\Store_TransactionRow','store_transaction_id',null,'StoreTransactionRows');
	}
	
	function fromWarehouse($warehouse=false){
		if($warehouse)
			$this['from_warehouse_id'] = $warehouse->id;
		else
			return $this->ref('from_warehouse_id');
	}

	function toWarehouse($warehouse=false){
		if($warehouse)
			$this['to_warehouse_id'] = $warehouse->id;
		else
			return $this->ref('to_warehouse_id');
	}

	function addItem($item_id,$qty,$custom_fields,$customfield_value){
		$new_item = $this->ref('StoreTransactionRows');
		$new_item['store_transaction_id'] = $this->id;
		$new_item['item_id'] = $item_id;
		$new_item['qty'] = $qty;
		$new_item['customfield_generic_id'] = $custom_fields;
		$new_item['customfield_value_id'] = $customfield_value ;
		$new_item->save();

		return $this;
	}
}