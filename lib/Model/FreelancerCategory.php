<?php

namespace xepan\commerce;


/**
* 
*/
class Model_FreelancerCategory extends \xepan\base\Model_Table{
	public $table = "freelancer_category" ;
	public $acl = false;
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('status')->enum(['Active','Inactive']);

		$this->hasMany('xepan/commerce/FreelancerCatAndCustomerAssociation','freelancer_category_id');
	}
}