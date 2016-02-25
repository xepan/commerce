<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\commerce\Model_Document{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
					'Draft'=>['view','edit','delete','submit'],
					'Submitted'=>['view','edit','delete','approve','redesign','reject'],
					'Approved'=>['view','edit','delete','redesign','reject','send'],
					'Redesign'=>['view','edit','delete','submit','reject'],
					'Rejected'=>['view','edit','delete'],
					'Converted'=>['view','edit','delete','send']
					];

	function init(){
		parent::init();

		$document_j = $this->join('document.document_id');

		$document_j->hasOne('xepan\base\Contact','contact_id');
		$document_j->hasOne('xepan\commerce\TNC','tnc_id');

	}
}
