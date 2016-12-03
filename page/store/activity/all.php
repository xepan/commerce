<?php


namespace xepan\commerce;

class page_store_activity_all extends \xepan\base\Page{
	public $title="Store Activities";

	function init(){
		parent::init();
			
		$tabs = $this->add('Tabs');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Opening');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Purchase');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Purchae Return');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Adjustment Add');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Adjustment Removed');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Movement');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Issue');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Issue Submitted');
	
	}
}