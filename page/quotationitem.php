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
					
		$q_no = $this->add('xepan\base\View_Document',['action'=>$action],null,['page/quotation/detail']);
		$q_no->setIdField('document_id');
		$q_no->setModel($quotation,['qt_no','created_at','discount_amount','gross_amount','total_amount','net_amount'],
								   ['qt_no','created_at','discount_amount']);

		$q_no->form->getElement('discount_amount')->js('change')->_load('xepan-QSIP')->univ()->calculateQSIP();
		if($quotation->loaded()){
			$items = $q_no->addMany('Items',null,'item_info',['page/quotation/item'],'xepan\commerce\Grid_Quotation','xepan\commerce\CRUD_Quotation');
			$items->setModel($quotation->ref('xepan\commerce\QuotationItem'));
		}else{
			// $q_no->add('View',null,'item_info')->set('PLease save basic info first');
		}
		

	}

}