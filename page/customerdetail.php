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
		
		$contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		$contact_view->setModel($customer);

		$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
		$d->setIdField('contact_id');
		$d->setModel($customer,['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
								'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency'],
								['shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode',
								'billing_address','billing_city','billing_state','billing_country','billing_pincode','tin_no','pan_no','organization','currency_id']);
	}

	function defaultTemplate(){
		return ['page/customer/detail'];
	}
}
