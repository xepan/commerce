<?php

namespace xepan\commerce;

class Model_Invoice_PurchaseInvoice extends \xepan\commerce\Model_Invoice{
	function init(){
		parent::init();
		$this->addCondition('type','PurchaseInvoice');

	}
}
