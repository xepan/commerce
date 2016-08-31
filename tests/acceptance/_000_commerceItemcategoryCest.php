<?php

// namespace xepan\commerce;

// use \SuperUser;
// use \Codeception\Util\Locator;

class _000_commerceItemcategoryCest
{
	public function _before(SuperUser $I){} 

	public function _after(SuperUser $I){}

	// public function test_check_item_category(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item Category');
	// 	$i->waitForText('No matching records found');

	// }
	// public function test_add_item_category(SuperUser $i){
	// 	$i->login('management@xavoc.com');
	// 	$i->clickMenu('Commerce->Item Category');
	// 	$i->waitForText('No matching records found');
	// 	$i->click('Add Category');
	// 	$i->waitForText('Adding new Category');
	// 	$i->fillAtkField('name','Online');
	// 	$i->fillAtkField('display_sequence','1');
	// 	$i->fillAtkField('alt_text','Online');
	// 	$i->click('Add');
	// 	$i->wait(2);
	// 	$i->click('Add Category');
	// 	$i->waitForText('Adding new Category');
	// 	$i->fillAtkField('name','Online');
	// 	$i->click('Add');
	// 	$i->waitForText('Name value "Online" already exists');
	// 	$i->fillAtkField('name','offline');
	// 	$i->fillAtkField('display_sequence','2');
	// 	$i->fillAtkField('alt_text','offline');
	// 	$i->click('Add');
	// }

}