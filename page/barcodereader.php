<?php 
 namespace xepan\commerce;
 class page_barcodereader extends \xepan\base\Page{

	public $title='Bar Codes';

	function init(){
		parent::init();

		$barcodereader = $this->add('xepan\commerce\Model_BarCodeReader');
		$crud = $this->add('xepan\hr\CRUD',null,null,['view/barcodereader/grid']);

		$crud->setModel($barcodereader);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
	}

}  