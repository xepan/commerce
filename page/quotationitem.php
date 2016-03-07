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

 class page_quotationitem extends \Page {
	public $title='Quotation Item';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$quotation = $this->add('xepan\commerce\Model_Quotation')->tryLoadBy('id',$this->api->stickyGET('document_id'));
					
		$q_no = $this->add('xepan\base\View_Document',['action'=>$action,'id_field_on_reload'=>'document_id'],null,['page/quotation/detail']);
		$q_no->setModel($quotation,['qt_no','created_at','discount_voucher_amount','gross_amount','total_amount','net_amount'],
								   ['qt_no','created_at','discount_voucher_amount']);

		if($quotation->loaded()){
			$items = $q_no->addMany('Items',null,'item_info',['page/quotation/item'],null,'xepan\commerce\CRUD_Quotation');
			$items->setModel($quotation->ref('xepan\commerce\QuotationItem'));
		}else{
			// $q_no->add('View',null,'item_info')->set('PLease save basic info first');
		}
		

	}

}