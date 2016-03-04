<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Condition extends \xepan\commerce\Model_Document{
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		$doc_j=$this->join('item.document_id');

		//TODO
		// $doc_j->hasOne('xProduction/Phase','department_phase_id');

		$doc_j->hasOne('xepan/commerce/Item/Quantity_Set','quantity_set_id');
		$doc_j->hasOne('xepan/commerce/Item/CustomField_Value','customfield_value_id');
		
	}
} 
 
	

