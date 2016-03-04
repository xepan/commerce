<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Condition extends \xepan\base\Model_Table{
 	public $table = "quantity_condition";
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		//TODO
		// $doc_j->hasOne('xProduction/Phase','department_phase_id');

		$this->hasOne('xepan\commerce\Item_Quantity_Set','quantity_set_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		
	}
} 
 
	

