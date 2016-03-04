<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Generic extends \xepan\base\Model_Table{
 	public $table = "customfield_generic";

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('display_type')->enum(['Line','DropDown','Color'])->mandatory(true);
		$this->addField('sequence_order')->type('Number')->hint('show in asceding order');
		$this->addField('is_filterable')->type('boolean');
		$this->addField('type')->enum(['CustomField','Specification','UserChoice'])->mandatory(true)->system(true);

	}
} 
 
	

