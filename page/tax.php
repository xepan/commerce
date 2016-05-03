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

		if($crud->isEditing()){
			$crud->form->setLayout('view\form\tax');
		}

		$crud->setModel($tax,['name','percentage']);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(50);
	}

}  