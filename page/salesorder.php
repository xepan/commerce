<?php 
 namespace xepan\commerce;
 class page_salesorder extends \Page{

	public $title='SalesOrder';

	function init(){
		parent::init();

		$sorder=$this->add('xepan\commerce\Model_SalesOrder');

		$crud=$this->add('xepan\hr\CRUD',['action_page'=>'xepan_commerce_salesorderdetail'],null,['view/order/sale/grid']);

		$crud->setModel($sorder);
		$crud->grid->addQuickSearch(['name']);
	}

}  