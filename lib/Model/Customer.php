<?php

/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


 namespace xepan\commerce;

 class Model_Customer extends \xepan\base\Model_Contact{
 	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'InActive'=>['view','edit','delete','activate']
					];

	function init(){
		parent::init();

		$cust_j=$this->join('customer.contact_id');


		$cust_j->addField('billing_address')->type('text');
		$cust_j->addField('billing_city');
		$cust_j->addField('billing_state');
		$cust_j->addField('billing_country');
		$cust_j->addField('billing_pincode');
		
		$cust_j->addField('shipping_address')->type('text');
		$cust_j->addField('shipping_city');
		$cust_j->addField('shipping_state');
		$cust_j->addField('shipping_country');
		$cust_j->addField('shipping_pincode');
		
		//Extra Organization Specific Fields
		$cust_j->addField('sale_person');
		$cust_j->addField('sale_person_contact')->hint('add comma seperated multiple value');
		$cust_j->addField('account_person');
		$cust_j->addField('account_person_contact')->hint('add comma seperated multiple value');

		$this->addCondition('type','Customer');		
		
	}

	//activate Customer
	function activate(){
		$this['status']='Active';
		$this->saveAndUnload();
	}

	//deactivate Customer
	function deactivate(){
		$this['status']='InActive';
		$this->saveAndUnload();
	}
}
 
    