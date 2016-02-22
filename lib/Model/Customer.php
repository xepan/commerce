<?php

 namespace xepan\commerce;
 class Model_Customer extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		//$this->hasOne('xepan\base\Epan','epan_id');
		
		// Basic Field
		$cust_j=$this->join('customer.contact_id');
		$this->addCondition('type','customer');
		
		//$this->hasOne('xepan\base\Epan','epan_id');

		//$cust_j=$this->join('email.email');
		//$cust_j->addField('is_active')->enum(['Active','Inactive']);
	}
}

 