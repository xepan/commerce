<?php
namespace xepan\commerce;
class page_store_warehouse extends \xepan\base\Page{
	public $title="Store Warehouse";
	function init(){
		parent::init();

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/store/warehouse-grid']);
		$crud->grid->addPaginator(10);
		$crud->grid->addQuickSearch(['name']);
		$crud->setModel('xepan\commerce\Store_Warehouse');
	}
}