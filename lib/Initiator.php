<?php

namespace xepan\commerce;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_commerce';

	function init(){
		parent::init();
		$this->routePages('xepan_commerce');
		$this->addLocation(array('template'=>'templates','js'=>'templates/js'))
		->setBaseURL('../vendor/xepan/commerce/');
		
		if($this->app->is_admin){
			$m = $this->app->top_menu->addMenu('Commerce');
			$m->addItem('Item','xepan_commerce_item');
			$m->addItem('Item Category','xepan_commerce_category');
			$m->addItem('Customer','xepan_commerce_customer');
			$m->addItem('Supplier','xepan_commerce_supplier');
			$m->addItem('Quotation','xepan_commerce_quotation');
			$m->addItem('Sales Order','xepan_commerce_salesorder');
			$m->addItem('Sales Invoice','xepan_commerce_salesinvoice');
			$m->addItem('Purchase Order','xepan_commerce_purchaseorder');
			$m->addItem('Purchase Invoice','xepan_commerce_purchaseinvoice');
			$m->addItem('Configuration','xepan_commerce_setting');
			$m->addItem('Terms And Condition','xepan_commerce_tnc');
		}
	}
}
