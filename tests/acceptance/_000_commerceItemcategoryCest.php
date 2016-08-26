<?php

namespace _005commerce;

use \SuperUser;
use \Codeception\Util\Locator;

class _000_commerceItemcategoryCest
{
	public function _before(SuperUser $I){} 

	public function _after(SuperUser $I){}

	public function test_check_item_category(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Item Category');
		$i->waitForText('No matching records found');

	}
	public function test_add_item_category(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Item Category');
		$i->waitForText('No matching records found');
		$i->click('Add Category');
		$i->waitForText('Adding new Category');
		$i->fillAtkField('name','Online');
		$i->fillAtkField('display_sequence','1');
		$i->fillAtkField('alt_text','Online');
		$i->click('Add');
		$i->wait(2);
		$i->click('Add Category');
		$i->waitForText('Adding new Category');
		$i->fillAtkField('name','Online');
		$i->click('Add');
		$i->waitForText('Name value "Online" already exists');
		$i->fillAtkField('name','offline');
		$i->fillAtkField('display_sequence','2');
		$i->fillAtkField('alt_text','offline');
		$i->click('Add');
	}

	public function test_check_item(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Item');
		$i->waitForText('No matching records found');

	}
	public function test_add_new_item(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Item');
		$i->waitForText('No matching records found');
		$i->click('Add Item');
		$i->waitForText('Item Template');
		$i->click(['css'=>'.main-box-header .row .col-md-3:nth-child(1) .btn']);
		$i->waitForText('Item Details');
		$i->fillAtkField('name',' ');
		$i->waitForText('Name must not be empty');
		$i->fillAtkField('name','High Availability Database Server');
		$i->click('Save');
		$i->waitForText('Sku must not be empty');
		$i->fillAtkField('sku','HADS-001');
		$i->select2Option("status",['text'=>'Publish']);
		$i->fillAtkField('expiry_date','30/08/2016');
		$i->select2Option("designer_id",['text'=>'Super User']);
		$i->CheckCheckBox('is_saleable');
		$i->CheckCheckBox('is_allowuploadable');
		$i->CheckCheckBox('is_renewable');
		$i->select2Option("remind_to",['text'=>'Both']);
		$i->select2Option("unit",['text'=>'Months']);
		$i->CheckCheckBox('website_display');
		$i->fillAtkField('terms_and_conditions','Wait For Releasing');
		$i->save();
	}
}