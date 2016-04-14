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

		$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
		$d->setIdField('contact_id');
		$d->setModel($customer,['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
								'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency'],
								['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
								'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency_id']);
		
/**

		Orders

*/

			$ord = $this->add('xepan\commerce\Model_SalesOrder')
			->addCondition('contact_id',$customer->id);
			$crud_ord = $this->add('xepan\hr\CRUD',null,'orders',['view/customer/order/grid']);
			$crud_ord->setModel($ord);
			$crud_ord->grid->addQuickSearch(['orders']);

/**

		Invoices

*/
			$inv = $this->add('xepan\commerce\Model_SalesInvoice')
			->addCondition('contact_id',$customer->id);
			$crud_inv = $this->add('xepan\hr\CRUD',null,'invoices',['view/customer/invoice/grid']);
			$crud_inv->setModel($inv);
			$crud_inv->grid->addQuickSearch(['invoices']);		

	}

	function defaultTemplate(){
		return ['page/customer/detail'];
	}
}
