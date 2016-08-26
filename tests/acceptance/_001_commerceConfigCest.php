<?php

// namespace xepan\commerce;

// use \SuperUser;
// use \Codeception\Util\Locator;

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

}