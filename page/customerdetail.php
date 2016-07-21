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

class page_customerdetail extends \xepan\base\Page {
	public $title='Customer Details';
	public $breadcrumb=['Home'=>'index','Customers'=>'xepan_commerce_customer','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';

		$customer= $this->add('xepan\commerce\Model_Customer')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		if($action=="add"){

			$contact_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\commerce\Model_Customer','view_document_class'=>'xepan\hr\View_Document'],'contact_view_full_width');
			$contact_view->document_view->effective_template->tryDel('im_and_events_andrelation');
			$contact_view->document_view->effective_template->tryDel('email_and_phone');
			$contact_view->document_view->effective_template->tryDel('online_status_wrapper');
			$contact_view->document_view->effective_template->del('avatar_wrapper');
			$contact_view->document_view->effective_template->tryDel('contact_since_wrapper');
			$contact_view->document_view->effective_template->tryDel('contact_type_wrapper');
			$contact_view->document_view->effective_template->tryDel('send_email_sms_wrapper');
			$this->template->del('details');
			$contact_view->setStyle(['width'=>'50%','margin'=>'auto']);
		}else{
			$contact_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\commerce\Model_Customer','view_document_class'=>'xepan\hr\View_Document'],'contact_view');
		}

		$contact_view->setModel($customer);
		if($customer->loaded()){
			$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
			$d->setIdField('contact_id');
			$d->setModel($customer,['shipping_address','shipping_city','shipping_pincode',
				'billing_address','billing_city','billing_pincode','tin_no','pan_no','organization','currency','user','remark'],
				['shipping_address','shipping_city','shipping_state_id','shipping_country_id','shipping_pincode','same_as_billing_address',
				'billing_address','billing_city','billing_state','billing_state_id','billing_country','billing_country_id','billing_pincode','tin_no','pan_no','organization','currency_id','user_id','remark']);

			$b_country = $d->form->getElement('billing_country_id');
			$b_state = $d->form->getElement('billing_state_id');
			
			if($this->app->stickyGET('billing_country_id'))
				$b_state->getModel()->addCondition('country_id',$_GET['billing_country_id'])->setOrder('name','asc');
				$b_country->js('change',$b_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$b_state->name]),'billing_country_id'=>$b_country->js()->val()]));
			
			$s_country = $d->form->getElement('shipping_country_id');
			$s_state = $d->form->getElement('shipping_state_id');
			
			if($this->app->stickyGET('shipping_country_id'))
				$s_state->getModel()->addCondition('country_id',$_GET['shipping_country_id'])->setOrder('name','asc');
				$s_country->js('change',$s_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$s_state->name]),'shipping_country_id'=>$s_country->js()->val()]));
		
			$s_a = $d->form->getElement('shipping_address');
			$s_cr = $s_country;
			$s_s = $s_state;
			$s_c = $d->form->getElement('shipping_city');
			$s_p = $d->form->getElement('shipping_pincode');

			$b_a = $d->form->getElement('billing_address');
			$b_cr = $b_country;
			$b_s = $b_state;
			$b_c = $d->form->getElement('billing_city');
			$b_p = $d->form->getElement('billing_pincode');

			$js = array(	
				$s_a->js()->val($b_a->js()->val()),
				$s_cr->js()->val($b_cr->js()->val()),
				$s_s->js()->val($b_s->js()->val()),
				$s_c->js()->val($b_c->js()->val()),
				$s_p->js()->val($b_p->js()->val())
				);

			$same_as_billing_field = $d->form->getElement('same_as_billing_address');

			$same_as_billing_field->js(true)->univ()->bindConditionalShow([
				''=>['shipping_address'],
				'*'=>['']
			],'div#shipping_address');

			$same_as_billing_field->js('change',$this->js()->val($js));


			

/**

		Orders

*/

			$ord = $this->add('xepan\commerce\Model_SalesOrder')
			->addCondition('contact_id',$customer->id);
			$crud_ord = $this->add('xepan\hr\CRUD',
							['action_page'=>'xepan_commerce_salesorderdetail'],
							'orders',
							['view/customer/order/grid']
						);

			$crud_ord->setModel($ord)->setOrder('created_at','desc');
			$crud_ord->grid->addQuickSearch(['orders']);

			if(!$crud_ord->isEditing()){
				$crud_ord->grid->js('click')->_selector('.do-view-customer-order')->univ()->frameURL('Salesorder Detail',[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesorder-id]')->data('id')]);
			}

/**

		Invoices

*/
			$inv = $this->add('xepan\commerce\Model_SalesInvoice')
			->addCondition('contact_id',$customer->id);
			$crud_inv = $this->add('xepan\hr\CRUD',
							['action_page'=>'xepan_commerce_salesinvoicedetail'],
							'invoices',
							['view/customer/invoice/grid']
						);
			$crud_inv->setModel($inv)->setOrder('created_at','desc');
			$crud_inv->grid->addQuickSearch(['invoices']);		
			
			if(!$crud_inv->isEditing()){
				$crud_inv->grid->js('click')->_selector('.do-view-customer-invoice')->univ()->frameURL('Salesinvoice Detail',[$this->api->url('xepan_commerce_salesinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesinvoice-id]')->data('id')]);
			}
		}
/*
	Activity

*/
		if($customer->loaded()){
			$activity_view = $this->add('xepan\base\Grid',null,'activity',['view/activity/activity-grid']);

			$activity=$this->add('xepan\base\Model_Activity');
			$activity->addCondition('related_contact_id',$_GET['contact_id']);
			$activity->tryLoadAny();
			$activity_view->setModel($activity);
		}


	}

	function defaultTemplate(){
		return ['page/customer/detail'];
	}
}
