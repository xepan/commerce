<?php
namespace xepan\commerce;
class page_configurationsidebar extends \xepan\base\Page{
	function init(){
		parent::init();
		$this->app->side_menu->addItem(['CustomField','icon'=>'fa fa-cog'],'xepan_commerce_customfield')->setAttr(['title'=>'Item CustomField']);
		$this->app->side_menu->addItem(['Specification','icon'=>'fa fa-magic xepan-effect-yellow'],'xepan_commerce_specification')->setAttr(['title'=>'Item Specification']);
		$this->app->side_menu->addItem(['Payment Gate Way','icon'=>'fa fa-cc-mastercard'],'xepan_commerce_paymentgateway')->setAttr(['title'=>'Payment GateWay']);

	}
}