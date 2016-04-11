<?php
namespace xepan\commerce;
class page_configurationsidebar extends \Page{
	function init(){
		parent::init();
		$this->app->side_menu->addItem('Configuration','xepan_commerce_setting');
		$this->app->side_menu->addItem('CustomField','xepan_commerce_customfield');
		$this->app->side_menu->addItem('Specification','xepan_commerce_specification');
		$this->app->side_menu->addItem('Payment Gate Way','xepan_commerce_paymentgateway');

	}
}