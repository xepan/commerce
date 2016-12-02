<?php

namespace xepan\commerce;


/**
* 
*/
class Model_FreelancerCatAndCustomerAssociation extends \xepan\base\Model_Table{
	public $table = "freelancer_cat_customer_asso";
	
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_Customer','customer_id');
		$this->hasOne('xepan\commerce\Model_FreelancerCategory','freelancer_category_id');
	}
}