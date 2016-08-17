<?php 
 namespace xepan\commerce;
 class page_salesorder extends \xepan\base\Page{

	public $title='Sale Order';

	function init(){
		parent::init();

		$saleorder = $this->add('xepan\commerce\Model_SalesOrder');
		$saleorder->setOrder('id','desc');
		$saleorder->add('xepan\commerce\Controller_SideBarStatusFilter');

		$saleorder->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});

		$saleorder->addExpression('contact_type',$saleorder->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesorderdetail']
						,null,
						['view/order/sale/grid']);
		$crud->grid->addHook('formatRow',function($g){
			$inv = $this->add('xepan\commerce\Model_SalesInvoice');
			$inv->addCondition('related_qsp_master_id',$g->model['document_no']);
			$inv->tryLoadAny();

			if($inv->loaded()){
				$g->current_row['inv_no']= '[inv:'.$inv['document_no'].']';
				$g->current_row['sales_invoice_id']= $inv->id;
			}
				
			$contact = $this->add('xepan\base\Model_Contact');
			$contact->load($g->model['contact_id']);
			
			if($contact['organization'])
				$g->current_row['organization_name']= $contact['organization'];
			else
				$g->current_row['organization_name']= $contact['name'];
			
			$g->current_row['contact_url']= $g->model['contact_type'];
			
			if($g->model['from'] == 'Online')
				$g->current_row['online_icon']= "fa-shopping-cart";
		});

		$crud->setModel($saleorder);
		$crud->grid->addPaginator(50);
		$frm=$crud->grid->addQuickSearch(['document_no','contact']);
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Sales Order Details',[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesorder-id]')->data('id')]);
			$crud->grid->js('click')->_selector('.do-view-customer-frame')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
			$crud->grid->js('click')->_selector('.order-invoice-number')->univ()->frameURL('Invoice Detail',[$this->api->url('xepan_commerce_salesinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->data('salesinvoice-id')]);
		}
	}
}  