<?php

namespace xepan\commerce;

class Model_Item_Stock extends \xepan\commerce\Model_Item{
	function init(){
		parent::init();

		$this->getElement('total_orders')->destroy();
		$this->getElement('total_sales')->destroy();

		
	}
}