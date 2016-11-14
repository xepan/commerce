<?php

namespace xepan\commerce;

class page_store_dispatch extends \xepan\commerce\page_store_dispatchabstract{
	public $title="Dispatch Order Item";

	function init(){
		parent::init();

		$order_dispatch_m = $this->add('xepan\commerce\Model_Store_OrderItemDispatch');
		$order_dispatch_m->addCondition('due_quantity','>',0);
		$order_dispatch_m->setOrder('id','desc');
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($order_dispatch_m,['qsp_master','name','quantity','toreceived_quantity','received_quantity','shipped_quantity','delivered_quantity','due_quantity','status']);
		$crud->grid->addPaginator(30);

	}
}