<?php

namespace _005commerce;

use \SuperUser;
use \Codeception\Util\Locator;

class _001_commerceConfigCest
{
	public function _before(SuperUser $I){} 

	public function _after(SuperUser $I){}

	public function test_check_config(SuperUser $i){
		$i->login('management@xavoc.com');
		$i->clickMenu('Commerce->Configuration');
		$i->waitForText('Custom Fields');
		$i->clickMenu('Specification');
		$i->waitPageLoad();
		$i->waitForText('Specification');
		$i->clickMenu('Payment Gate Way');
		$i->waitForText('Payment Gate Way');
		$i->clickMenu('Layouts');
		$i->waitForText('Layouts');
		$i->click(['css'=>'a#tab-salesorder']);
		$i->waitForText('Sales Order Layout:');

	}

	public function test_add_item_basic(SuperUser $i){
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