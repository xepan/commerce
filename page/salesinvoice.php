<?php 
 namespace xepan\commerce;
 class page_salesinvoice extends \Page{

	public $title='Sales Invoice';

	function init(){
		parent::init();

		$salesinvoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$salesinvoice->addExpression('contact_type',$salesinvoice->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesinvoicedetail']
						,null,
						['view/invoice/sale/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});

		$crud->setModel($salesinvoice);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(10);

	}

}  