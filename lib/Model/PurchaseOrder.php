<?php

namespace xepan\commerce;

class Model_PurchaseOrder extends \xepan\commerce\Model_QSP_Master{
	function init(){
		parent::init();
		$this->addCondition('type','PurchaseOrder');

	}
}
