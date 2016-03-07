<?php 
 namespace xepan\commerce;
 class page_purchaseorder extends \Page{

	public $title='PurchaseOrder';

	function init(){
		parent::init();

		$porder=$this->add('xepan\commerce\Model_Order_PurchaseOrder');

		$crud=$this->add('xepan\hr\CRUD',['action_page'=>'xepan_commerce_purchaseorderdetail'],null,['view/order/purchase/grid']);

		$crud->setModel($porder);
		$crud->grid->addQuickSearch(['name']);
	}

}  