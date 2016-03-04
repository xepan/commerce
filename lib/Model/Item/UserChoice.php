<?php 

 namespace xepan\commerce;

 class Model_Item_UserChoice extends \xepan\commerce\Model_Item_CustomField_Generic{
	function init(){
		parent::init();

		$this->addCondition('type','UserChoice');
		
		$this->hasMany('xepan/commerce/Item/CustomField_Association','customfield_generic_id');
	}
} 
 
	

