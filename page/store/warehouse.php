<?php
namespace xepan\commerce;
class page_store_warehouse extends \xepan\base\Page{
	public $title="Store Warehouse";
	function init(){
		parent::init();

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/store/warehouse-grid']);
		$crud->form->setLayout('view\store\form\warehouse');
		$crud->grid->addPaginator(10);
		$crud->grid->addQuickSearch(['first_name','last_name']);
		$crud->setModel('xepan\commerce\Store_Warehouse',['first_name','last_name','country_id','state_id','city','address','pin_code','organization']);
	}
}