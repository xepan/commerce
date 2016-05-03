<?php 
 namespace xepan\commerce;
 class page_salesinvoice extends \Page{

	public $title='Sales Invoice';

	function init(){
		parent::init();

		
		$salesinvoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$salesinvoice->add('xepan\commerce\Controller_SideBarStatusFilter');

		$salesinvoice->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});


		$salesinvoice->addExpression('contact_type',$salesinvoice->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesinvoicedetail']
						,null,
						['view/invoice/sale/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});

		$salesinvoice->setOrder('created_at','DESC');
		$crud->setModel($salesinvoice)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$frm=$crud->grid->addQuickSearch(['contact','document_no','net_amount_self_currency']);
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
	}
} 
