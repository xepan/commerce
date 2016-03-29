<?php
namespace xepan\commerce;
class Model_Store_Warehouse extends \xepan\base\Model_Contact{
	// public $table="store_warehouse";
	public $acl=false;

	function init(){
		parent::init();

		$this->addCondition('type','Warehouse');

		$this->hasMany('xepan\commerce\Store_Transaction','from_warehouse_id',null,'FromTransactions');
		$this->hasMany('xepan\commerce\Store_Transaction','to_warehouse_id',null,'ToTransactions');
	}


	function newPurchaseReceive($purchase_order,$warehouse_id){
		$m = $this->add('xepan\commerce\Model_Store_Transaction');
			$m['document_type'] = 'Purchase';
			$m['from_warehouse_id'] = $purchase_order['contact_id'];
			$m['to_warehouse_id'] = $warehouse_id;
			$m['related_document_id']=$purchase_order->id;	
			
		$m->save();
		return $m;
	}

}