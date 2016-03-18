<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','manage_attachments'],
				'InProgess'=>['view','edit','delete','cancel','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments'],
				// 'Returned'=>['view','edit','delete','manage_attachments']

				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');

	}
}
