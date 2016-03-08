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
					
		$q_no = $this->add('xepan\base\View_Document',['action'=>$action],null,['view/qsp/master']);
		$q_no->setIdField('document_id');
		$q_no->setModel($quotation,['contact','document_no','created_at','discount_amount','gross_amount','total_amount','net_amount','billing_address','billing_city','billing_state','billing_country','billing_pincode','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode'],['contact_id','document_no','created_at','discount_amount','billing_address','billing_city','billing_state','billing_country','billing_pincode','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode']);


		$q_no->form->getElement('discount_amount')->js('change')->_load('xepan-QSIP')->univ()->calculateQSIP();
		if($quotation->loaded()){
			$items = $q_no->addMany('Items',null,'item_info',['view/qsp/details'],'xepan\commerce\Grid_Quotation','xepan\commerce\CRUD_Quotation');
			$items->setModel($quotation->ref('Details'));
		}else{
			// $q_no->add('View',null,'item_info')->set('PLease save basic info first');
		}
		

	}

}