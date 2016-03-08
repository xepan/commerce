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

class page_purchaseorderdetail extends \Page {
	public $title='PurchaseOrder Invoice';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$porder= $this->add('xepan\commerce\Model_Order_PurchaseOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$pinvoice_no = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/order/purchase/detail','basic_info']);
		$pinvoice_no->setModel($porder,['name'],['name']);

		$pinvoice_item = $this->add('xepan\base\View_Document',['action'=>$action],'item_info',['page/order/purchase/detail','item_info']);
		$pinvoice_item->setModel($porder ,['discount_voucher_amount','gross_amount','total_amount','net_amount'],
									 ['discount_voucher_amount','gross_amount','total_amount','net_amount']);
	}

	function defaultTemplate(){
		return ['page/order/purchase/detail'];
	}
}
