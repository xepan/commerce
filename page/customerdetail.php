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

class page_customerdetail extends \Page {
	public $title='Customer Details';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$customer= $this->add('xepan\commerce\Model_Customer')->tryLoadBy('id',$this->api->stickyGET('contact_id'));
		
		$contact_view = $this->add('xepan\base\View_Contact',null,'contact_view');
		$contact_view->setModel($customer);

		$d = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/customer/detail','basic_info']);
		$d->setModel($customer,['address','shipping_address','billing_address'],['address','shipping_address','billing_address']);
	}

	function defaultTemplate(){
		return ['page/customer/detail'];
	}
}
