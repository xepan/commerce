<?php

namespace xepan\commerce;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_commerce';

	function init(){
		parent::init();
		$this->routePages('xepan_commerce');
		$this->addLocation(array('template'=>'templates'));
		
		$m = $this->app->top_menu->addMenu('Commerce');
		$m->addItem('Item','xepan_commerce_item');
		$m->addItem('Customer','xepan_commerce_customer');
		$m->addItem('Supplier','xepan_commerce_supplier');
		$m->addItem('Sales','xepan_commerce_salesorder');
		$m->addItem('SalesInvoice','xepan_commerce_salesinvoice');
		$m->addItem('Purchase','xepan_commerce_purchaseorder');
		$m->addItem('PurchaseInvoice','xepan_commerce_purchaseinvoice');
		$m->addItem('Quotation','xepan_commerce_quotations');
	}
}
