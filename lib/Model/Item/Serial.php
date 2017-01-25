<?php

namespace xepan\commerce;

class Model_Item_Serial extends \xepan\commerce\Model_Table{
	public $table = "item_serial" ;
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
	}

}