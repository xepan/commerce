<?php

namespace xepan\commerce;

class Model_Store_Transaction extends \xepan\base\Model_Table{
	public $table="store_transaction";
	// public $acl=false;
	public $status = ['ToReceived','Received'];
	public $actions=[
				'ToReceived'=>['view','edit','delete','receive'],
			];
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','from_warehouse_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','to_warehouse_id');
		$this->hasOne('xepan\production\Jobcard','jobcard_id');
		
		$this->addField('related_document_id')->sortable(true); //Sale Ordre/Purchase
		$this->addField('type'); //Purchase/Sale/Dispatch
		// $this->addCondition('type','Store_Transaction');
		$this->addField('created_at')->defaultValue(date('Y-m-d'))->sortable(true);
		$this->addField('created_by_id')->defaultValue($this->app->employee->id)->sortable(true);
		$this->addField('status')->enum($this->status)->sortable(true);

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

	function transactionRow(){
		$this->ref('StoreTransactionRows');
	}

	function addItem($qsp_detail_id,$qty,$jobcard_detail,$custom_fields,$customfield_value){
		$new_item = $this->ref('StoreTransactionRows');
		$new_item['store_transaction_id'] = $this->id;
		$new_item['qsp_detail_id'] = $qsp_detail_id;
		$new_item['qty'] = $qty;
		$new_item['jobcard_detail_id'] = $jobcard_detail;
		$new_item['customfield_generic_id'] = $custom_fields;
		$new_item['customfield_value_id'] = $customfield_value ;
		$new_item->save();

		return $this;
	}

	function receive(){
		$tra_row=$this->add('xepan\commerce\Model_Store_TransactionRow');
		$tra_row->addCondition('store_transaction_id',$this->id);
		$tra_row->tryLoadAny();
		// throw new \Exception($tra_row->id, 1);
		
		$job_detail=$this->add('xepan\production\Model_Jobcard_Detail');
		$job_detail->addCondition('id',$tra_row['jobcard_detail_id']);
		$job_detail->tryLoadAny();

		if($job_detail->loaded()){
			$job_detail['status']="Completed";
			$job_detail->save();

			$this['status']="Received";
			$this->save();
			return true;
		}else{
			throw new \Exception("Model Job Detail Must be Loaded", 1);
		}
		
	}
}