<?php

namespace xepan\commerce;

class Model_Item_Serial extends \xepan\base\Model_Table{
	public $table = "item_serial";
	// public $acl_type = 'Item_Serial';
	public $title_field = "serial_no";
	public $acl = 'xepan\commerce\Model_Item';

	function init(){
		parent::init();

		$this->addField('serial_no');

		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\commerce\Item_Serializable','item_id');
		$this->hasOne('xepan\commerce\Model_PurchaseOrder','purchase_order_id');
		$this->hasOne('xepan\commerce\Model_PurchaseInvoice','purchase_invoice_id');
		$this->hasOne('xepan\commerce\Model_SalesOrder','sale_order_id');
		$this->hasOne('xepan\commerce\Model_SalesInvoice','sale_invoice_id');
		$this->hasOne('xepan\commerce\Model_Store_DispatchRequest','dispatch_id');
		$this->hasOne('xepan\commerce\Model_Store_Transaction','transaction_id');
		
		$this->hasOne('xepan\commerce\Model_QSP_Detail','purchase_order_detail_id');
		$this->hasOne('xepan\commerce\Model_QSP_Detail','purchase_invoice_detail_id');
		$this->hasOne('xepan\commerce\Model_QSP_Detail','sale_order_detail_id');
		$this->hasOne('xepan\commerce\Model_QSP_Detail','sale_invoice_detail_id');
		$this->hasOne('xepan\commerce\Model_Store_TransactionRow','transaction_row_id');
		$this->hasOne('xepan\commerce\Model_Store_TransactionRow','dispatch_row_id');

		$this->addField('is_return')->type('boolean')->defaultValue(false);
		$this->addField('is_available')->type('boolean')->defaultValue(true);
		$this->addField('narration')->type('text');
		
		$this->addHook('beforeSave',[$this,'isSerialNumberExist']);
	}


	function isSerialNumberExist(){
		$old_model = $this->add('xepan\commerce\Model_Item_Serial');
		$old_model
				->addCondition('item_id',$this['item_id'])
				->addCondition('serial_no',$this['serial_no'])
				->addCondition('purchase_order_id',$this['purchase_order_id'])
				->addCondition('purchase_invoice_id',$this['purchase_invoice_id'])
				->addCondition('sale_order_id',$this['sale_order_id'])
				->addCondition('sale_invoice_id',$this['sale_invoice_id'])
				->addCondition('dispatch_id',$this['dispatch_id'])
				->addCondition('transaction_id',$this['transaction_id'])
				->addCondition('id','<>',$this->id)
				;

		$old_model->tryLoadAny();
		if($old_model->loaded())
			throw new \Exception("this serial no ".$this['serial_no']." already exists");
	}

	function isAvailable(){
		if(!$this->loaded()) throw new \Exception("item serial model must loaded");
		
		return $this['is_available'];
	}

	/**
		data_array = 
					[
						[
						'item_id'=>,
						'serial_no'=>,
						'purchase_order_id'=>,
						'purchase_invoice_id'=>,
						'sale_order_id'=>,
						'sale_invoice_id'=>,
						'dispatch_id'=>,
						'transaction_id'=>,
						'is_available'=>,
						'narration'=>,
						'qsp_detail_id'=>
					]
				]
	*/
	function addSerialNo($data_array){
		if(!is_array($data_array) OR count($data_array) == 0)
			throw new \Exception("must pass data i array");
			
		$model = $this->add('xepan\commerce\Model_Item_Serial');
		foreach ($data_array as $list) {
			foreach ($list as $field_name => $value) {
				$model[$field_name] = $value;
			}
		}
		$model->save();
	}


	function markUsed(){
		if(!$this->loaded()) throw new \Exception("loaded model not found");
		
		$this['is_available'] = false;
		return $this->save();
	}

	function markUnused(){
		if(!$this->loaded()) throw new \Exception("loaded model not found");

		$this['is_available'] = true;
		return $this->save();	
	}

	function setDispatched($dispatch_id){
		if(!$this->loaded()) throw new \Exception("loaded model not found");

		$this['dispatch_id'] = $dispatch_id;
		return $this->save();
	}
}