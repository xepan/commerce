<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');

	}
}
