<?php

// namespace xepan\commerce;

// use \SuperUser;
// use \Codeception\Util\Locator;

class _002_commerceItemCest
{
	public function _before(SuperUser $I){} 

	public function _after(SuperUser $I){}

	// public function test_add_item_basic(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item');
	// 	$i->waitForText('No matching records found');
	// 	$i->click('Add Item');
	// 	$i->waitForText('Item Template');
	// 	$i->click(['css'=>'.main-box-header .row .col-md-3:nth-child(1) .btn']);
	// 	$i->waitForText('Item Details');
	// 	$i->fillAtkField('name','');
	// 	$i->click('Save');
	// 	$i->waitForText('Name must not be empty');
	// 	$i->fillAtkField('name','High Availability Database Cluster Server');
	// 	$i->click('Save');
	// 	$i->waitForText('Sku must not be empty');
	// 	$i->fillAtkField('sku','HADS-001');
	// 	$i->select2Option("status",['text'=>'Published']);
	// 	$i->fillAtkField('expiry_date','30/08/2016');
	// 	$i->select2Option("designer_id",['text'=>'Super User']);
	// 	$i->checkNiceCheckbox('is_saleable');
	// 	$i->checkNiceCheckbox('is_allowuploadable');
	// 	$i->checkNiceCheckbox('is_renewable');
	// 	$i->select2Option("remind_to",['text'=>'Both']);
	// 	$i->select2Option("renewable_unit",['text'=>'Months']);
	// 	$i->checkCheckBox('website_display');
	// 	$i->fillAtkField('terms_and_conditions','Wait For Releasing');
	// 	$i->click('Save');
	// }

	// public function test_add_item_atribute(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item');
	// 	// $i->click(['css'=>'table tbody tr td:nth-child(1) a.do-view-item-detail']);
	// 	// $i->closeDialog();
	// 	$i->waitForText('High Availability Database Cluster Server');
	// 	$i->click(['css'=>'.table tbody tr td a.pb_edit']);
	// 	/*Specification*/
	// 	$i->selectorClick('[href=#tab-Attribute]');
	// 	$i->see('No matching records found');
	// 	$i->click('Add Specification');
	// 	$i->waitForText('Adding new Specification');
	// 	$i->select2Option('customfield_generic_id',['text'=>'']);
	// 	$i->click('Add');
	// 	$i->waitForText('Customfield_generic_id must not be empty');		
	// 	$i->select2Option('customfield_generic_id',['text'=>'Customizable']);
	// 	$i->select2Option('status',['text'=>'Active']);
	// 	$i->click('Add');

	// 	/*Custom Field*/
	// 	$i->selectorClick('[href=#tab-CustomField]');
	// 	$i->see('No matching records found');
	// 	$i->click('Add CustomField');
	// 	$i->waitForText('Adding new CustomField');
	// 	$i->select2Option('customfield_generic_id',['text'=>'']);
	// 	$i->click('Add');
	// 	$i->waitForText('Customfield_generic_id must not be empty');		
	// 	$i->select2Option('customfield_generic_id',['text'=>'Color']);
	// 	$i->select2Option('department_id',['text'=>'Company']);
	// 	$i->checkCheckBox('can_effect_stock');
	// 	$i->select2Option('status',['text'=>'Active']);
	// 	$i->click('Add');

	// 	/*Filters*/
	// 	$i->selectorClick('[href=#tab-Filter]');
	// 	$i->see('No matching records found');
	// 	$i->click('Add Filters');
	// 	$i->waitForText('Adding new Filters');
	// 	$i->select2Option('customfield_generic_id',['text'=>'']);
	// 	$i->click('Add');
	// 	$i->waitForText('Customfield_generic_id must not be empty');		
	// 	$i->select2Option('customfield_generic_id',['text'=>'Customizable']);
	// 	$i->select2Option('status',['text'=>'Active']);
	// 	$i->click('Add');
	// }

	// public function test_add_item_qtyAndSet(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item');
	// 	// $i->click(['css'=>'table tbody tr td:nth-child(1) a.do-view-item-detail']);
	// 	// $i->closeDialog();
	// 	$i->waitForText('High Availability Database Cluster Server');
	// 	$i->click(['css'=>'.table tbody tr td a.pb_edit']);	

	// 	/* Basic Price */
	// 	$i->selectorClick('[href=#tab-QtyPrice]');
	// 	$i->waitForText('Price & Quantity');
	// 	$i->fillAtkField('sale_price','50000');
	// 	$i->fillAtkField('original_price','75000');
	// 	$i->fillAtkField('maximum_order_qty','2');
	// 	$i->click('Save');
		
	// 	/*Rate Chart*/
	// 	$i->selectorClick('[href=#tab-qty-set-contition]');
	// 	$i->waitForText('No matching records found');
	// 	$i->click('Add Item Quantity Set');
	// 	$i->waitForText('Adding new item Quantity Set');
	// 	$i->fillAtkField('name','default');
	// 	$i->fillAtkField('qty','1');
	// 	$i->fillAtkField('old_price','75000');
	// 	$i->fillAtkField('price','50000');
	// 	$i->checkCheckBox('is_default');
	// 	$i->click('Add');
	// 	$i->dontSee('No matching records found');
	// 	// $i->click('condition');
	// 	// $i->waitForText('Managing Quantity Set Condition');
	// 	// $->click('Add Conditions');
	// 	// $->waitForText('Adding new Conditions');
	// }

	// public function test_add_item_category(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item');
	// 	// $i->click(['css'=>'table tbody tr td:nth-child(1) a.do-view-item-detail']);
	// 	// $i->closeDialog();
	// 	$i->waitForText('High Availability Database Cluster Server');
	// 	$i->click(['css'=>'.table tbody tr td a.pb_edit']);	
	// 	$i->selectorClick('[href=#tab-Category]');
	// 	$i->waitForText("CATEGORY");
	// 	$i->selectorClick('input#cb_2399');
	// 	$i->click('Update');
	// 	$i->waitForText('Category Associated');
	// }	

	// public function test_add_item_media(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item');
	// 	$i->waitForText('High Availability Database Cluster Server');
	// 	$i->click(['css'=>'.table tbody tr td a.pb_edit']);	
	// 	$i->selectorClick('[href=#tab-Extra]');

	// 	/*Item images*/
	// 	$i->waitForText("No matching records found");
	// 	$i->click('Add Item Image');
	// 	$i->waitForText('Adding new Item Image');
	// 	// $i->select2Option('customfield_value_id',['text'=>'']);
	// 	$i->click('Add');

	// 	/*SEO*/
	// 	$i->selectorClick('[href=##tab-SEO]');
	// 	$i->waitForText('Meta Title:');
	// 	$i->fillAtkField('meta_title','High Availability Database Cluster Server');
	// 	$i->fillAtkField('meta_description','High Availability Database Cluster Server');
	// 	$i->fillAtkField('tags','High Availability Database Cluster Server');
	// 	$i->click('Update');
		
	// }
	
	// public function test_add_item_productionPhases(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item');
	// 	// $i->click(['css'=>'table tbody tr td:nth-child(1) a.do-view-item-detail']);
	// 	// $i->closeDialog();
	// 	$i->waitForText('High Availability Database Cluster Server');
	// 	$i->click(['css'=>'.table tbody tr td a.pb_edit']);	
	// 	$i->selectorClick('[href=#tab-ProductionPhase]');
	// 	$i->waitForText("CONSUMPTION");
	// 	$i->selectorClick('input#cb_2390');
	// 	$i->click('Update');
	// 	$i->waitForText('Department Added to this Item');
	// }	
}