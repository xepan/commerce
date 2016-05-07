<?php
namespace xepan\commerce;

class page_store_transaction extends \Page{
	public $title="Store Transaction";
	function init(){
		parent::init();
		$transaction=$this->add('xepan\commerce\Model_Store_Transaction');
		$crud=$this->add('xepan\hr\CRUD',null,null,['view/store/transaction-grid']);
		$crud->grid->addPaginator(10);
		$crud->grid->addQuickSearch(['name']);
		$crud->setModel($transaction);
	}
}