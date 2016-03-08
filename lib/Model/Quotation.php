<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\hr\Model_QSP_Master{
	public $status = [];
	public $actions = [];

	public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','Quotation');

	}
}
