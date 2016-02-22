<?php

 namespace xepan\commerce;
 class Model_Customerprofile extends \Model_Table{
 	public $table='customer';

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		
		// Basic Field
		$this->addField('customer_name')->mandatory(true);
		$this->addField('company_name')->mandatory(true);
		$this->addField('customer_email');
		$this->addField('contact_no');
		$this->addField('status')->enum(['Active','Inactive']);
	}
}

