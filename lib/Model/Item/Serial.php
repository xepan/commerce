<?php

namespace xepan\commerce;

class Model_Item_Serial extends \xepan\base\Model_Table{
	public $table = "item_serial";
	public $acl_type = 'Item_Serial';

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item_Serializable','item_id');
		$this->addField('serial_no');
		$this->addField('is_return');
		$this->addField('purchase_order_id');
		$this->addField('purchase_invoice_id');
		$this->addField('sale_order_id');
		$this->addField('sale_invoice_id');
		$this->addField('dispatch_id');
		$this->addField('transaction_id');
		$this->addField('is_available');
		$this->addField('narration')->type('text');
		
		$this->addHook('beforeSave',[$this,'isExist']);
	}

	function isExist($m){
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
				;

		$old_model->tryLoadAny();
		if($old_model->loaded())
			throw new \Exception("this serial no ".$this['serial_no']." already exists");		
	}

}