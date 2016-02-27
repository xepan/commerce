<?php

 namespace xepan\commerce;
 
 class Model_Supplier extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		$supl_j=$this->join('supplier.contact_id');
		$supl_j->addField('tin_no');
		$supl_j->addField('company_address');
		$supl_j->addField('company_name');
		
		$this->addCondition('type','Supplier');
	}
}

 