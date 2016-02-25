<?php

 namespace xepan\commerce;
 class Model_Salesorder extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		//$this->hasOne('xepan\base\Epan','epan_id');
		
		// Basic Field
		$supl_j=$this->join('salesorder.contact_id');
		$this->addCondition('type','customer');

		$supl_j=$this->join('document.document_type');
		$this->addCondition('type','invoice');

		$supl_j=$this->join('invoice.order_id');
		$this->addCondition('type','salesinvoice');

	}
}

 