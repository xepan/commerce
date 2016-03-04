<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Generic extends \xepan\commerce\Model_Document{
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		$doc_j=$this->join('item.document_id');

		$doc_j->addField('name');
		$doc_j->addField('display_type')->enum(['line','DropDown','Color'])->mandatory(type)->defaultValue('DropDown');
		$doc_j->addField('sequence_order')->type('Number')->hint('show in asceding order');
		$doc_j->addField('is_filterable')->type('boolean');		
	}
} 
 
	

