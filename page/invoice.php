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

class page_invoice extends \Page {
	public $title='SalesOrder Invoice';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$sorder= $this->add('xepan\commerce\Model_Order_SalesOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$sinvoice_no = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/order/sales/invoice','basic_info']);
		$sinvoice_no->setModel($sorder,['name'],['name']);

		$sinvoice_item = $this->add('xepan\base\View_Document',['action'=>$action],'item_info',['page/order/sales/invoice','item_info']);
		$sinvoice_item->setModel($sorder ,['discount_voucher_amount','gross_amount','total_amount','net_amount'],
									 ['discount_voucher_amount','gross_amount','total_amount','net_amount']);
	}

	function defaultTemplate(){
		return ['page/order/sales/invoice'];
	}
}
