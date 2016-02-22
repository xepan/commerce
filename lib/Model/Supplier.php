<?php

 namespace xepan\commerce;
 class Model_Supplier extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		//$this->hasOne('xepan\base\Epan','epan_id');
		
		// Basic Field
		$supl_j=$this->join('supplier.contact_id');
		$this->addCondition('type','supplier');
	}
}

 