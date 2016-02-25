<?php

 namespace xepan\commerce;

 class Model_Customer extends \xepan\base\Model_Contact{

	function init(){
		parent::init();

		$cust_j=$this->join('customer.contact_id');

		$this->addCondition('type','Customer');

		// BUG : add billing and Shipping addresse
		$cust_j->addField('shipping_address')->type('text');
		$cust_j->addField('shipping_city');
		$cust_j->addField('shipping_state');
		$cust_j->addField('shipping_country');
		$cust_j->addField('shipping_pincode');

		$cust_j->addField('billing_address')->type('text');
		$cust_j->addField('billing_city');
		$cust_j->addField('billing_state');
		$cust_j->addField('billing_country');
		$cust_j->addField('billing_pincode');
		
		
	}
}
 
    