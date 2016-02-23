<?php

 namespace xepan\commerce;

 class Model_Customer extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		$cust_j=$this->join('customer.contact_id');
		// BUG : add billing and Shipping addresse
		$cust_j->addField('shipping_address');
		$cust_j->addField('billing_address');
		
		$this->addCondition('type','Customer');
		
	}
}
 
    