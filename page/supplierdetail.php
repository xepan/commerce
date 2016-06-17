<?php
 
/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class page_supplierdetail extends \xepan\base\Page {
	public $title='Supplier Details';
	public $breadcrumb=['Home'=>'index','Supplier'=>'xepan_commerce_supplier','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$supplier= $this->add('xepan\commerce\Model_Supplier')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		if($action=="add"){

			$contact_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\commerce\Model_Supplier','view_document_class'=>'xepan\hr\View_Document'],'contact_view_full_width');
			$contact_view->document_view->effective_template->del('im_and_events_andrelation');
			$contact_view->document_view->effective_template->del('email_and_phone');
			$contact_view->document_view->effective_template->tryDel('online_status_wrapper');
			$contact_view->document_view->effective_template->del('avatar_wrapper');
			$contact_view->document_view->effective_template->tryDel('contact_since_wrapper');
			$contact_view->document_view->effective_template->tryDel('contact_type_wrapper');
			$contact_view->document_view->effective_template->tryDel('send_email_sms_wrapper');
			$this->template->del('details');
			$contact_view->setStyle(['width'=>'50%','margin'=>'auto']);
		}else{
			$contact_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\commerce\Model_Supplier','view_document_class'=>'xepan\hr\View_Document'],'contact_view');
		}		
		$contact_view->setModel($supplier);

		if($supplier->loaded()){
			$d = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'contact_id'],'basic_info',['page/supplier/detail','basic_info']);
			$d->setModel($supplier,['tin_no','address','pan_no','organization','city','currency','pin_code','remark'],['tin_no','address','pan_no','organization','city','state_id','country_id','currency_id','pin_code','remark']);

			$country = $d->form->getElement('country_id');
			$state = $d->form->getElement('state_id');
			
			if($this->app->stickyGET('country_id'))
				$state->getModel()->addCondition('country_id',$_GET['country_id'])->setOrder('name','asc');
				$country->js('change',$state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state->name]),'country_id'=>$country->js()->val()]));
			

	/**

			Orders

	*/

				$ord = $this->add('xepan\commerce\Model_PurchaseOrder')
				->addCondition('contact_id',$supplier->id);
				$crud_ord = $this->add('xepan\hr\CRUD',
									['action_page'=>'xepan_commerce_purchaseorderdetail'],
									'orders',
									['view/supplier/order/grid']);
				$crud_ord->setModel($ord);
				$crud_ord->grid->addQuickSearch(['orders']);

				if(!$crud_ord->isEditing()){
					$crud_ord->grid->js('click')->_selector('.do-view-supplier-order')->univ()->frameURL('Purchaseorder Detail',[$this->api->url('xepan_commerce_purchaseorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-purchaseorder-id]')->data('id')]);
				}
	/**

			Invoices

	*/
				$inv = $this->add('xepan\commerce\Model_PurchaseInvoice')
				->addCondition('contact_id',$supplier->id);
				$crud_inv = $this->add('xepan\hr\CRUD',
										['action_page'=>'xepan_commerce_purchaseinvoicedetail'],
										'invoices',
										['view/supplier/invoice/grid']);
				$crud_inv->setModel($inv);
				$crud_inv->grid->addQuickSearch(['invoices']);
				
				if(!$crud_inv->isEditing()){
					$crud_inv->grid->js('click')->_selector('.do-view-supplier-invoice')->univ()->frameURL('Purchaseinvoice Detail',[$this->api->url('xepan_commerce_purchaseinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-purchaseinvoice-id]')->data('id')]);
				}
	/*
		Activity

	*/
				$activity_view = $this->add('xepan\base\Grid',null,'activity',['view/activity/activity-grid']);

				$activity=$this->add('xepan\base\Model_Activity');
				$activity->addCondition('contact_id',$_GET['contact_id']);
				$activity->tryLoadAny();
				$activity_view->setModel($activity);
		}


	}



	function defaultTemplate(){
		return ['page/supplier/detail'];
	}
}