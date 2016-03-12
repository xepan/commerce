<?php 
 namespace xepan\commerce;
 class page_customfield extends \xepan\commerce\page_configurationsidebar{

	public $title='Custom Fields';

	function init(){
		parent::init();
		$cf_model = $this->add('xepan\commerce\Model_Item_CustomField');

		$crud=$this->add('xepan\hr\CRUD','null',null,['view/item/customfield']);

		$crud->setModel($cf_model);
		$crud->grid->addQuickSearch(['name']);
	}

}  