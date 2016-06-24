<?php 

 namespace xepan\commerce;

 class Model_Filter extends \xepan\commerce\Model_Item_CustomField_Generic{
	function init(){
		parent::init();

		$this->addCondition('is_filterable','true');
		$this->setOrder('sequence_order','desc');
	}
} 
 
	

