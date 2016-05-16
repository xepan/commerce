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
		
		$contact_view = $this->add('xepan\base\View_Contact',['acl'=>"xepan\commerce\Model_Customer"],'contact_view');
		// $contact_view->acl="xepan\commerce\Model_Customer";
		$contact_view->setModel($customer);


		if($customer->loaded()){
			$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
			$d->setIdField('contact_id');
			$d->setModel($customer,['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
				'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency','user','remark'],
				['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode','same_as_billing_address',
				'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency_id','user_id','remark']);


			$s_a = $d->form->getElement('shipping_address');
			$s_c = $d->form->getElement('shipping_city');
			$s_p = $d->form->getElement('shipping_pincode');
			$s_s = $d->form->getElement('shipping_state');
			$s_cr = $d->form->getElement('shipping_country');

			$b_a = $d->form->getElement('billing_address');
			$b_c = $d->form->getElement('billing_city');
			$b_p = $d->form->getElement('billing_pincode');
			$b_s = $d->form->getElement('billing_state');
			$b_cr = $d->form->getElement('billing_country');

			$js = array(	
				$s_a->js()->val($b_a->js()->val()),
				$s_c->js()->val($b_c->js()->val()),
				$s_p->js()->val($b_p->js()->val()),
				$s_s->js()->val($b_s->js()->val()),
				$s_cr->js()->val($b_cr->js()->val())
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
		}
/*
	Activity

*/
		if($customer->loaded()){
			$activity_view = $this->add('xepan\base\Grid',null,'activity',['view/activity/activity-grid']);

			$activity=$this->add('xepan\base\Model_Activity');
			$activity->addCondition('contact_id',$_GET['contact_id']);
			$activity->tryLoadAny();
			$activity_view->setModel($activity);
		}


	}

	function defaultTemplate(){
		return ['page/customer/detail'];
	}
}
