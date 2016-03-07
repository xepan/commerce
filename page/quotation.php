<?php 
 namespace xepan\commerce;
 class page_quotation extends \Page{

	public $title='Quotations';

	function init(){
		parent::init();

		$quotation = $this->add('xepan\commerce\Model_Quotation');
		$quotation->addExpression('contact_type',$quotation->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_quotationitem']
						,null,
						['view/quotation/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']=$g->model['contact_type'];
		});

		$crud->setModel($quotation);
		$crud->grid->addQuickSearch(['name']);

	}

}  