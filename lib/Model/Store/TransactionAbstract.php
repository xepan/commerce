<?php

namespace xepan\commerce;

class Model_Store_TransactionAbstract extends \xepan\base\Model_Table{
	public $table="store_transaction";
	
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','from_warehouse_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','to_warehouse_id');
		$this->hasOne('xepan\production\Jobcard','jobcard_id');
		$this->addField('type');
		
		$this->addField('related_document_id')->sortable(true); //Sale Ordre/Purchase
		$this->addField('document_type'); //Purchase/Sale/Dispatch/Deliver
		$this->addField('created_at')->defaultValue(date('Y-m-d'))->sortable(true);
		$this->addField('created_by_id')->defaultValue($this->app->employee->id)->sortable(true);
		$this->addField('status')->enum($this->status)->sortable(true);

		//Delivered Option or shipping tracking code
		$this->addField('delivery_via');
		$this->addField('delivery_reference');
		$this->addField('shipping_address')->type('text');
		$this->addField('shipping_charge')->type('money');
		$this->addField('narration')->type('text');
		$this->addField('tracking_code')->type('text');


		$this->hasMany('xepan\commerce\Store_TransactionRow','store_transaction_id',null,'StoreTransactionRows');
		$this->addExpression('toreceived')->set(function($m,$q){
			$to_received = $m->refSQL('StoreTransactionRows')
							->addCondition('status','ToReceived')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$to_received]);
		})->sortable(true);	

		$this->addExpression('received')->set(function($m,$q){
			$to_received = $m->refSQL('StoreTransactionRows')
							->addCondition('status','Received')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$to_received]);
		})->sortable(true);

		$this->addExpression('department')->set(function($m,$q){
			return $m->refSQL('jobcard_id')->fieldQuery('department');
		});

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

	function transactionRow(){
		$this->ref('StoreTransactionRows');
	}

	function addItem($qsp_detail_id,$qty,$jobcard_detail_id,$custom_fields,$customfield_value,$status="ToReceived"){
		$new_item = $this->ref('StoreTransactionRows');
		$new_item['store_transaction_id'] = $this->id;
		$new_item['qsp_detail_id'] = $qsp_detail_id;
		$new_item['quantity'] = $qty;
		$new_item['jobcard_detail_id'] = $jobcard_detail_id;
		$new_item['customfield_generic_id'] = $custom_fields;
		$new_item['customfield_value_id'] = $customfield_value ;
		$new_item['status'] = $status;
		$new_item->save();

		return $this;
	}

	function saleOrder(){
		if(!$this->loaded()){
			throw new \Exception("sale order not found");
		}

		$sale_order = $this->add('xepan\commerce\Model_SalesOrder');
		$sale_order->tryLoadBy('id',$this['related_document_id']);
		if(!$sale_order->loaded())
			return false;

		return $sale_order;
	}
}