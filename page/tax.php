<?php 
 namespace xepan\commerce;
 class page_tax extends \Page{

	public $title='Vat & Tax';

	function init(){
		parent::init();

		$tax=$this->add('xepan\commerce\Model_Taxation');
		$crud=$this->add('xepan\hr\CRUD',null,
						null,
						['view/tax/grid']
					);

		
		$crud->setModel($tax);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(10);
	}

}  