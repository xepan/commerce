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

 class page_quotationdetail extends \Page {
	public $title='Quotation Item';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$quotation = $this->add('xepan\commerce\Model_Quotation')->tryLoadBy('id',$this->api->stickyGET('document_id'));
					
		$q_no = $this->add('xepan\base\View_Document',['action'=>$action],'basic_info',['page/quotation/item','basic_info']);
		$q_no->setModel($quotation,['name'],['name']);

		$q_item = $this->add('xepan\base\View_Document',['action'=>$action],'item_info',['page/quotation/item','item_info']);
		$q_item->setModel($quotation,['discount_voucher_amount','gross_amount','total_amount','net_amount'],
									 ['discount_voucher_amount','gross_amount','total_amount','net_amount']);
	}

	function defaultTemplate(){
		return ['page/quotation/item'];

	}
}


