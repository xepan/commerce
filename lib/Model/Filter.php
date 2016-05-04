<?php 

 namespace xepan\commerce;

 class Model_Filter extends \xepan\commerce\Model_Item_CustomField_Generic{
	function init(){
		parent::init();

		$this->addCondition('is_filterable','true');
		$this->setOrder('name','asc');
		$this->setOrder('sequence_order','asc');
	}
} 
 
	

