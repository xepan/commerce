<?php 
 namespace xepan\commerce;
 class page_salesinvoice extends \xepan\base\Page{

	public $title='Sales Invoices';

	function init(){
		parent::init();

		// datetime used in widget monthlysalesinvoice
		$monthyear = $this->app->stickyGET('monthyear');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		
		$customer_id = $this->app->stickyGET('customer_id');
		
		$salesinvoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$salesinvoice->add('xepan\commerce\Controller_SideBarStatusFilter');

		// FOR WIDGET MONTHLY INVOICES		
		if($monthyear){
			$salesinvoice->addExpression('monthyear')->set('DATE_FORMAT(created_at,"%M %Y")');
			$salesinvoice->addCondition('monthyear',$monthyear);
		}

		if($customer_id)
			$salesinvoice->addCondition('contact_id',$customer_id);

		$salesinvoice->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});


		$salesinvoice->addExpression('contact_type',$salesinvoice->refSQL('contact_id')->fieldQuery('type'));

		$salesinvoice->addExpression('contact_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('name');
		});
		$salesinvoice->addExpression('contact_organization_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('organization');
		});

		$salesinvoice->addExpression('organization_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization_name]) OR trim([organization_name])="" ,[contact_name],[organization_name])',
						[
							'contact_name'=>$m->getElement('contact_name'),
							'organization_name'=>$m->getElement('contact_organization_name')
						]
					);
		});

		$salesinvoice->addExpression('ord_no',function($m,$q){
			return $m->refSQL('related_qsp_master_id')->fieldQuery('document_no');
		});

		$salesinvoice->addExpression('sales_order_id',function($m,$q){
			return $m->refSQL('related_qsp_master_id')->fieldQuery('id');
		});

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesinvoicedetail']
						,null,
						['view/invoice/sale/grid']);


		$salesinvoice->setOrder('created_at','DESC');
		$crud->setModel($salesinvoice)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$frm=$crud->grid->addQuickSearch(['contact_name','organization_name','document_no','net_amount_self_currency']);
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Sales Invoice Details',[$this->api->url('xepan_commerce_salesinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesinvoice-id]')->data('id')]);
			$crud->grid->js('click')->_selector('.do-view-customer-frame')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
			$crud->grid->js('click')->_selector('.order-invoice-number')->univ()->frameURL('Order Detail',[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->data('salesorder-id')]);
		}
	}
} 
