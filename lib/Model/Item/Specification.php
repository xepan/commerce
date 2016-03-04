<?php 

 namespace xepan\commerce;

 class Model_Item_Specification extends \xepan\commerce\Model_Item_GenericCustomField{
	function init(){
		parent::init();

		$this->addCondition('type','Specification');

		$this->hasMany('xepan/commerce/Item/CustomField_Association','customfield_generic_id');
	}
} 
 
	

