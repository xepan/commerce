<?php
namespace xepan\commerce;
class page_itemtype extends \Page{
	function init(){
		parent::init();
		$this->app->side_menu->addItem('Check Box Filter');
		$this->app->side_menu->addItem('Is_Saleable','');
		$this->app->side_menu->addItem('Is_Purchasable','');
		$this->app->side_menu->addItem('Is_Productionable','');
		$this->app->side_menu->addItem('Is_AllowUploadable','');
		$this->app->side_menu->addItem('Website_Display','');
	}
}