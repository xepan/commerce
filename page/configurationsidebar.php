<?php
namespace xepan\commerce;
class page_configurationsidebar extends \xepan\base\Page{
	function init(){
		parent::init();
		$this->app->side_menu->addItem(['Tax','icon'=>'fa fa-percent'],'xepan_commerce_tax')->setAttr(['title'=>'Tax']);
		$this->app->side_menu->addItem(['CustomField','icon'=>'fa fa-cog'],'xepan_commerce_customfield')->setAttr(['title'=>'Item CustomField']);
		$this->app->side_menu->addItem(['Specification','icon'=>'fa fa-magic xepan-effect-yellow'],'xepan_commerce_specification')->setAttr(['title'=>'Item Specification']);
		$this->app->side_menu->addItem(['Payment Gate Way','icon'=>'fa fa-cc-mastercard'],'xepan_commerce_paymentgateway')->setAttr(['title'=>'Payment GateWay']);
		$this->app->side_menu->addItem(['Layouts','icon'=>'fa fa-th'],'xepan_commerce_layouts')->setAttr(['title'=>'Layouts']);
		$this->app->side_menu->addItem(['Designer Library','icon'=>'fa fa-th'],'xepan_commerce_designerlibraryimages')->setAttr(['title'=>'Designer Library Images']);
		$this->app->side_menu->addItem(['Fonts','icon'=>'fa fa-th'],'xepan_commerce_font')->setAttr(['title'=>'Fonts']);
		$this->app->side_menu->addItem(['Terms And Condition','icon'=>'fa fa-check-square'],'xepan_commerce_tnc')->setAttr(['title'=>'Term & Condition']);
		$this->app->side_menu->addItem(['Shipping Rule','icon'=>'fa fa-truck'],'xepan_commerce_shippingrule')->setAttr(['title'=>'Shipping Rule']);
		$this->app->side_menu->addItem(['Amount Standard','icon'=>'fa fa-dollar'],'xepan_commerce_amountstandard')->setAttr(['title'=>'Amount Standard']);

	}
}