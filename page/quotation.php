<?php 
 namespace xepan\commerce;
 class page_quotation extends \Page{

	public $title='Quotations';

	function init(){
		parent::init();

		$quotation = $this->add('xepan\commerce\Model_Quotation');

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_quotationitem']
						,null,
						['view/quotation/grid']);

		$crud->setModel($quotation);
		$crud->grid->addQuickSearch(['name']);
	}

}  