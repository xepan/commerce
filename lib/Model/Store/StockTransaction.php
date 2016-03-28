<?php
namespace xepan\commerce;
class Model_Store_StockTransaction extends \xepan\base\Model_Table{
	public $table="store_transaction";
	public $acl=false;
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','warehouse_id');
		$this->addField('related_document_id');
		$this->addField('document_type');
		$this->hasMany('xepan\commerce\Store_StockTransactionRow','store_transaction_id',null,'StoreTransactionRows');
	}
}