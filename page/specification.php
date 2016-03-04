<?php 
 namespace xepan\commerce;
 class page_specification extends \Page{

	public $title='Specifications';

	function init(){
		parent::init();

		$specification = $this->add('xepan\commerce\Model_Item_Specification');

		$crud=$this->add('xepan\hr\CRUD','null',null,['view/item/specification']);

		$crud->setModel($specification);
		$crud->grid->addQuickSearch(['name']);
	}

}  