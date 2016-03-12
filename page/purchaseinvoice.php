<?php 
 namespace xepan\commerce;
 class page_purchaseinvoice extends \Page{

	public $title='PurchaseInvoice';

	function init(){
		parent::init();

		$purchaseinvoice = $this->add('xepan\commerce\Model_PurchaseInvoice');
		$purchaseinvoice->addExpression('contact_type',$purchaseinvoice->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_purchaseinvoicedetail']
						,null,
						['view/invoice/purchase/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});
		
		$crud->setModel($purchaseinvoice);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(10);

	}

}  