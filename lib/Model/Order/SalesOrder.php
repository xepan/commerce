<?php

namespace xepan\commerce;

class Model_Order_SalesOrder extends \xepan\commerce\Model_Order{
	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');

	}
}
