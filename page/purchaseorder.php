<?php 
 namespace xepan\commerce;
 class page_purchaseorder extends \Page{

	public $title='Purchase Order';

	function init(){
		parent::init();

		$purchaseorder = $this->add('xepan\commerce\Model_PurchaseOrder');
		$purchaseorder->addExpression('contact_type',$purchaseorder->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_purchaseorderdetail']
						,null,
						['view/order/purchase/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});

		$crud->setModel($purchaseorder);
		$crud->grid->addQuickSearch(['name']);

	}

}  