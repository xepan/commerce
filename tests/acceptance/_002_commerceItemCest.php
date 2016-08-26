<?php

// namespace xepan\commerce;

// use \SuperUser;
// use \Codeception\Util\Locator;

class _002_commerceItemCest
{
	public function _before(SuperUser $I){} 

	public function _after(SuperUser $I){}

	public function test_add_item_basic(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Item');
		$i->waitForText('No matching records found');
		$i->click('Add Item');
		$i->waitForText('Item Template');
		$i->click(['css'=>'.main-box-header .row .col-md-3:nth-child(1) .btn']);
		$i->waitForText('Item Details');
		$i->fillAtkField('name','');
		$i->click('Save');
		$i->waitForText('Name must not be empty');
		$i->fillAtkField('name','High Availability Database Cluster Server');
		$i->click('Save');
		$i->waitForText('Sku must not be empty');
		$i->fillAtkField('sku','HADS-001');
		$i->select2Option("status",['text'=>'Publish']);
		$i->fillAtkField('expiry_date','30/08/2016');
		$i->select2Option("designer_id",['text'=>'Super User']);
		$i->checkNiceCheckbox('is_saleable');
		$i->checkNiceCheckbox('is_allowuploadable');
		$i->checkNiceCheckbox('is_renewable');
		$i->select2Option("remind_to",['text'=>'Both']);
		$i->select2Option("renewable_unit",['text'=>'Months']);
		$i->checkCheckBox('website_display');
		$i->fillAtkField('terms_and_conditions','Wait For Releasing');
		$i->click('Save');
	}

	public function test_add_item_atribute(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Item');
		// $i->click(['css'=>'table tbody tr td:nth-child(1) a.do-view-item-detail']);
		// $i->closeDialog();
		$i->waitForText('High Availability Database Cluster Server');
		$i->click(['css'=>'table tbody tr td:nth-child(5) a.pb_edit']);
		$i->click(['css'=>'a#tab-Attribute']);
		$i->waitForText(' No matching records found');
		$i->click('Add Specification');
		$i->waitForText(' Adding new Specification');
		
	}	
}