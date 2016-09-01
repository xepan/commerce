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

		$saleorder->addExpression('contact_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('name');
		});
		$saleorder->addExpression('contact_organization_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('organization');
		});

		$saleorder->addExpression('organization_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization_name]) OR trim([organization_name])="" ,[contact_name],[organization_name])',
						[
							'contact_name'=>$m->getElement('contact_name'),
							'organization_name'=>$m->getElement('contact_organization_name')
						]
					);
		});

		$saleorder->addExpression('inv_no',function($m,$q){
			// return $q->getField('id');
			return $q->expr('IFNULL([0],"")',
								[
									$m->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'acdf'])
										->addCondition('related_qsp_master_id',$q->getField('id'))
										->fieldQuery('document_no')	
								]
							);

		});

		$saleorder->addExpression('sales_invoice_id',function($m,$q){
			
			return $m->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'cdfd'])
										->addCondition('related_qsp_master_id',$q->getField('id'))
										->fieldQuery('id');

		});

		$saleorder->addExpression('contact_type',$saleorder->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesorderdetail']
						,null,
						['view/order/sale/grid']);
		
		$crud->grid->addHook('formatRow',function($g){
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