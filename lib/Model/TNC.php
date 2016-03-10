<?php

 namespace xepan\commerce;

 class Model_TNC extends \xepan\hr\Model_Document{
 	public $actions = ['*'=>'view','edit','delete'];
	function init(){
		parent::init();

		$document_j = $this->join('tnc.document_id');
		$document_j->addField('name');
		$document_j->addField('content')->type('text');

		$document_j->hasMany('xepan/commerce/QSP_Master','tnc_id');

		$this->addCondition('type','TNC');
	}
}
 
    