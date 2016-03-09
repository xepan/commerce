<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = [];
	public $actions = [];

	public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','SalesInvoice');

	}
}
