<?php

// namespace xepan\commerce;

// use \SuperUser;
// use \Codeception\Util\Locator;

class _001_commerceConfigCest
{
	public function _before(SuperUser $I){} 

	public function _after(SuperUser $I){}

	// public function test_check_config(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Configuration');
	// 	$i->waitForText('Custom Fields');
	// 	$i->clickMenu('Specification');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('Specification');
	// 	$i->clickMenu('Payment Gate Way');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('Payment Gate Way');
	// 	$i->clickMenu('Layouts');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('Layouts');
	// 		$i->selectorClick('[href=#tab-salesorder]');
	// 		$i->waitForText('Sales Order Layout:');
	// 		$i->selectorClick('[href=#tab-salesinvoice]');
	// 		$i->waitForText('Sales Invoice Layout:');
	// 		$i->selectorClick('[href=#tab-purchaseorder]');
	// 		$i->waitForText('Purchase Order Layout:');
	// 		$i->selectorClick('[href=#tab-purchaseinvoice]');
	// 		$i->waitForText('Purchase Invoice Layout:');
	// 		$i->selectorClick('[href=#tab-challan]');
	// 		$i->waitForText('Challan Layout:');
	// 	$i->click('Designer Library');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('Designer Library Images');
	// 	$i->click('Fonts');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('Select Fonts Flies to upload:');
	// 	$i->click('Terms And Condition');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('No matching records found');
	// 	$i->click('Shipping Rule');
	// 	$i->waitPageLoad();
	// 	$i->waitForText('No matching records found');
	// }

	function test_add_item_customField(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Configuration');
		$i->waitForText('Custom Fields');
		$i->click('Add Item CustomField');
		$i->waitPageLoad();
		$i->waitForText('show in asceding order');
		// $i->fillAktField('name','');
		$i->click('Add');
		$i->waitForText('Name must not be empty');
		$i->fillAktField('name','Color');
		$i->select2Option("display_type",['text'=>'']);
		$i->click('Add');
		$i->waitForText('Display_type must not be empty');
		$i->select2Option("display_type",['text'=>'Color']);
		$i->fillAktField('sequence_order','1');
		$i->checkCheckBox('is_filterable');
		$i->click('Add');
	}

	// function test_add_item_specification(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Configuration');
	// 	$i->waitForText('Custom Fields');
	// 	$i->click('Specification');
	// 	// $i->waitPageLoad();
	// 	$i->waitForText('Specification');
	// 	$i->click('Add Item Specification');
	// 	$i->waitForText('Adding new Item Specification');
	// 	$i->click('Add');
	// 	$i->waitForText('Name must not be empty');
	// 	$i->fillAktField('name','Customizable');
	// 	$i->fillAktField('sequence_order','2');
	// 	$i->checkCheckBox('is_filterable');
	// 	$i->click('Add');
	// }

	// function test_add_item_paymentGateway(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Configuration');
	// 	$i->waitForText('Custom Fields');
	// 	$i->click('Payment Gate Way');
	// 	// $i->waitPageLoad();
	// 	$i->waitForText('Payment Gate Way');
	// 	$i->click('Update');
	// }

	// // function test_add_item_layouts(SuperUser $i){}
	
	// function test_add_item_designerLibrary(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Configuration');
	// 	$i->waitForText('Custom Fields');
	// 	$i->click('Designer Library');
	// 	// $i->waitPageLoad();
	// 	$i->waitForText('Add Library Category');
	// 	$i->click('Add Library Category');
	// 	$i->waitForText('Adding new Library Category');
	// 	$i->click('Add');
	// 	$i->waitForText('Name must not be empty');
	// 	$i->fillAktField('name','My Design');
	// 	$i->click('Add');
	// }
}