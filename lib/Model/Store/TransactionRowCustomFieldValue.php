<?php

namespace xepan\commerce;

/**
* 
*/
class Model_Store_TransactionRowCustomFieldValue extends \SQL_Model{
	public $table="store_transaction_row_custom_field_value";
	function init(){
		parent::init();
		$this->hasOne('xepan\commerce\Item_CustomField_Generic','customfield_generic_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		$this->hasOne('xepan\commerce\Model_Store_TransactionRow','store_transaction_row_id');
		$this->addField('custom_name');
		$this->addField('custom_value');
	}
}