<?php
namespace xepan\commerce;

class page_store_transaction extends \Page{
	public $title="Store Warehouse";
	function init(){
		parent::init();

		$this->add('xepan\hr\CRUD',null,null,['view/store/transaction-grid'])->setModel('xepan\commerce\Store_Transaction');
	}
}