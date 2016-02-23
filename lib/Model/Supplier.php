<?php

 namespace xepan\commerce;
 
 class Model_Supplier extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		$supl_j=$this->join('supplier.contact_id');
		$supl_j->hasOne('xepan\base\Epan','epan_id');
		
		$this->addCondition('type','Supplier');
	}
}

 