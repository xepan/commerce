<?php
namespace xepan\commerce;
class page_store_warehouse extends \Page{
	public $title="Store Warehouse";
	function init(){
		parent::init();

		$this->add('xepan\hr\CRUD')->setModel('xepan\commerce\Store_Warehouse');
	}
}