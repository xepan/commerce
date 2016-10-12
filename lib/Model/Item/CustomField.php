<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField extends \xepan\commerce\Model_Item_CustomField_Generic{

 	public $acl_type = 'Item_CustomField';
	function init(){
		parent::init();

		$this->addCondition('type','CustomField');
		
		$this->hasMany('xepan/commerce/Item/CustomField_Association','customfield_generic_id');

		$this->setOrder('sequence_order','asc');
	}
} 
 
	

