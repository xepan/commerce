<?php 

 namespace xepan\commerce;

 class Model_Item_Specification extends \xepan\commerce\Model_Item_CustomField_Generic{
 	
 	public $acl_type = 'Item_Specification';

	function init(){
		parent::init();

		$this->addCondition('type','Specification');
		$this->addCondition('display_type','Line');

		$this->hasMany('xepan/commerce/Item/CustomField_Association','customfield_generic_id');
	}

	
} 
 
	

