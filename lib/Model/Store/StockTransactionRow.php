<?php
namespace xepan\commrece;
class Model_Store_StockTransactionRow extends \xepan\base\ModelTable{
	public $table="store_transaction_row";
	public $acl=false;
	function init(){
		parent::init();
		$this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\commrece\Store_StockTransaction','store_transaction_id');
		$this->hasOne('xepan\commrece\Item','item_id');
		$this->hasOne('xepan\commrece\Item_CustomField_Association','customfield_generic_id');
		$this->hasOne('xepan\commrece\Item_CustomField_Value','customfield_value_id');

		$this->addField('qty');
	}
}