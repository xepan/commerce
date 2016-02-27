<?php

 namespace xepan\commerce;

 class Model_TNC extends \xepan\commerce\Model_Document{
 	public $actions = ['*'=>'view','edit','delete'];
	function init(){
		parent::init();

		$document_j = $this->join('quotation.document_id');
		$document_j->addField('name');
		$document_j->addField('content')->type('text');

		$document_j->hasMany('xepan/commerce/Quotation','tnc_id');
	}
}
 
    