<?php

namespace xepan\commerce;

class page_reports_reportsidebar extends \xepan\base\Page{
	function init(){
		parent::init();

		$this->app->side_menu->addItem(['Customer','icon'=>'fa fa-user'],'xepan_commerce_reports_customer')->setAttr(['title'=>'Customer Report']);
		$this->app->side_menu->addItem(['Supplier','icon'=>'fa fa-user'],'xepan_commerce_reports_supplier')->setAttr(['title'=>'Supplier Report']);
		$this->app->side_menu->addItem(['Outsource Party','icon'=>'fa fa-user'],'xepan_commerce_reports_outsourceparty')->setAttr(['title'=>'Outsource Party Report']);
		$this->app->side_menu->addItem(['Sales Order','icon'=>'fa fa-shopping-cart'],'xepan_commerce_reports_salesorder')->setAttr(['title'=>'Sales Order Report']);
		$this->app->side_menu->addItem(['Sales Invoice','icon'=>'fa fa-shopping-cart'],'xepan_commerce_reports_salesinvoice')->setAttr(['title'=>'Sales Invoice Report']);
		$this->app->side_menu->addItem(['Purchase Order','icon'=>'fa fa-shopping-cart'],'xepan_commerce_reports_purchaseorder')->setAttr(['title'=>'Purchase Order Report']);
		$this->app->side_menu->addItem(['Purchase Invoice','icon'=>'fa fa-shopping-cart'],'xepan_commerce_reports_purchaseinvoice')->setAttr(['title'=>'Purchase Invoice Report']);
		$this->app->side_menu->addItem(['GST','icon'=>'fa fa-cog'],'xepan_commerce_reports_gst')->setAttr(['title'=>'GST Report']);
	}
}