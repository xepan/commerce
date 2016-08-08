<?php 
 namespace xepan\commerce;
 class page_salesinvoice extends \xepan\base\Page{

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
			$contact = $this->add('xepan\base\Model_Contact');
			$contact->load($g->model['contact_id']);
			$g->current_row['organization_name']= $contact['organization'];
			
			$g->current_row['contact_url']= $g->model['contact_type'];
		});

		$salesinvoice->setOrder('created_at','DESC');
		$crud->setModel($salesinvoice)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$frm=$crud->grid->addQuickSearch(['contact','document_no','net_amount_self_currency']);
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Sales Invoice Details',[$this->api->url('xepan_commerce_salesinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesinvoice-id]')->data('id')]);
			$crud->grid->js('click')->_selector('.do-view-customer-frame')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
		}
	}
} 
