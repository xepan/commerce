<?php

namespace xepan\commerce;

class Model_Store_TransactionRow extends \xepan\base\Model_Table{

	public $table="store_transaction_row";
	public $acl=false;

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\commerce\Store_Transaction','store_transaction_id');
		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_generic_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		$this->hasOne('xepan\production\Jobcard_Detail','jobcard_detail_id');

		$this->addField('qty');

		$this->addExpression('item_id')->set($this->refSQL('qsp_detail_id')->fieldQuery('item_id'));
		$this->addExpression('document_type')->set($this->refSQL('store_transaction_id')->fieldQuery('document_type'));

	}

	function item(){
		return $this->ref('item_id');
	}
	

}