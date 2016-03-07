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

class page_salesorderdetail extends \Page {
	public $title='Sales Order Detail';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$sorder= $this->add('xepan\commerce\Model_Order_SalesOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$sinvoice_no = $this->add('xepan\base\View_Document',['action'=>$action],null,['page/order/sales/invoice']);
		$sinvoice_no->setIdField('document_id');

		$sinvoice_no->setModel($sorder,['name','created_at','discount_amount','gross_amount','total_amount','net_amount'],['qt_no','created_at','discount_amount']);

	}
}
