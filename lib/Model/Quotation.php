<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\commerce\Model_QSP_Master{
	
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','approve'],
				'Approved'=>['view','edit','delete','redesign','reject','send'],
				'Redesign'=>['view','edit','delete','submit','reject'],
				'Rejected'=>['view','edit','delete'],
				'Converted'=>['view','edit','delete','send']
				];

	function init(){
		parent::init();

		$this->addCondition('type','Quotation');

	}
}
