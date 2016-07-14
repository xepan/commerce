<?php
namespace xepan\commerce;

class page_store_transaction extends \xepan\base\Page{
	public $title="Store Transaction";
	function init(){
		parent::init();
		$transaction=$this->add('xepan\commerce\Model_Store_Transaction');
		$crud=$this->add('xepan\hr\CRUD',null,null,['view/store/transaction-grid']);
		$crud->form->setLayout('view\store\form\transaction');
		$crud->setModel($transaction);
		$crud->grid->addPaginator(10);
		$crud->grid->addQuickSearch(['from_warehouse','to_warehouse']);
	}
}