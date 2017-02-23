<?php

namespace xepan\commerce;

class page_store_reports_storereportsidebar extends \xepan\base\Page{
	function init(){
		parent::init();

		$this->app->side_menu->addItem(['Stock Item','icon'=>'fa fa-shopping-cart'],'xepan_commerce_store_reports_itemstock')->setAttr(['title'=>'Stock Item']);
		$this->app->side_menu->addItem(['Short Item','icon'=>'fa fa-truck'],'xepan_commerce_store_reports_shortitem')->setAttr(['title'=>'Short Item']);
		$this->app->side_menu->addItem(['Item Report','icon'=>'fa fa-truck'],'xepan_commerce_store_reports_itemcustomfieldstock')->setAttr(['title'=>'Item stock according to custom field']);
	}
}