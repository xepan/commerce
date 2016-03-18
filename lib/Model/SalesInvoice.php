<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','approve','manage_attachments'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Due'=>['view','edit','delete','redesign','reject','send','manage_attachments'],
				'Paid'=>['view','edit','delete','send','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','SalesInvoice');

	}
}
