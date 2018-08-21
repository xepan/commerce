<?php


namespace xepan\commerce;

class page_store_activity_all extends \xepan\base\Page{
	public $title="Store Activities";

	function init(){
		parent::init();
			
		$tabs = $this->add('Tabs');
		$tabs->addTabURL('xepan_commerce_store_activity_opening','Opening');
		// $tabs->addTabURL('xepan_commerce_store_activity_purchase','Purchase');
		// $tabs->addTabURL('xepan_commerce_store_activity_purchasereturn','Purchase Return');
		$tabs->addTabURL('xepan_commerce_store_activity_adjustment','Adjustment');
		$tabs->addTabURL('xepan_commerce_store_activity_movement','Movement');
		$tabs->addTabURL('xepan_commerce_store_activity_issue','Issue');
		$tabs->addTabURL('xepan_commerce_store_activity_issuesubmitted','Issue Submitted');
		$tabs->addTabURL('xepan_commerce_store_activity_packageitem','Package Item');
		$tabs->addTabURL('xepan_commerce_store_transaction','Store Transaction');
	
	}
}