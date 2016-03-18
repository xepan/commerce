<?php

namespace xepan\commerce;

class Model_PurchaseOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','reject','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','reject','manage_attachments'],
				'InProgess'=>['view','edit','delete','cancel','MarkInProgress','manage_attachments'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Rejected'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments']
				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','PurchaseOrder');

	}
}
