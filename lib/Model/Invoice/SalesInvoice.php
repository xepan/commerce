<?php

namespace xepan\commerce;

class Model_Invoice_SalesInvoice extends \xepan\commerce\Model_Invoice{
	function init(){
		parent::init();
		$this->addCondition('type','SalesInvoice');
	}
}
