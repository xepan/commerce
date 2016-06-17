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

 class page_purchaseinvoicedetail extends \xepan\base\Page {
	public $title='Purchase Invoice Detail';
	public $breadcrumb=['Home'=>'index','Invoices'=>'xepan_commerce_purchaseinvoice','Detail'=>'#','New Purchase Invoice'=>'xepan_commerce_purchaseinvoicedetail&action=add'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$purchase_inv_dtl = $this->add('xepan\commerce\Model_PurchaseInvoice')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$view_field = 	[
							'contact_id',
							'document_no',
							'type',

							'billing_address',
							'billing_city',
							'billing_pincode',

							'shipping_address',
							'shipping_city',
							'shipping_pincode',

							'gross_amount',
							'discount_amount',
							'net_amount',
							'delivery_date',
							'tnc_text',
							'narration',
							'exchange_rate',
							'currency',

							//'priority_id',
							// 'payment_gateway_id',
							// 'transaction_reference',
							// 'transaction_response_data',
						];
		$form_field	=	[
							'contact_id',
							'document_no',
							'created_at',
							'due_date',
							
							'billing_address',
							'billing_country_id',
							'billing_state_id',
							'billing_city',
							'billing_pincode',

							'shipping_address',
							'shipping_country_id',
							'shipping_state_id',
							'shipping_city',
							'shipping_pincode',

							'discount_amount',
							'narration',
							'exchange_rate',
							'currency_id',
							// 'priority_id',
							// 'payment_gateway_id',
							// 'transaction_reference',
							// 'transaction_response_data',
							'tnc_id'
						];
		
		$dv = $this->add('xepan\commerce\View_QSPAddressJS')->set('');

		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$purchase_inv_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);

		$view->js('click')->_selector('a.new-qsp')->univ()->location($this->app->url(null,['action'=>'add','document_id'=>false]));
		
		if($action !='view'){
			$contact_field = $view->document->form->getElement('contact_id');
			$contact_field->model->addCondition('type','Supplier');

			$contact_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));
		}

		if($action !='add'){
			$lister = $view->document->add('Lister',null,'common_vat',['view/qsp/master','common_vat'])->setSource($purchase_inv_dtl->getCommnTaxAndAmount());
		}

		if($action=='edit'){
			$view->document->effective_template->setHTML('common_vat',$lister->getHtml());

			$item_m=$this->add('xepan\commerce\Model_Item');
			$detail_j=$item_m->join('qsp_detail.item_id');
			$detail_j->addField('detail_id','id');
			$item_m->addCondition('detail_id','in',$view->document_item->model->fieldQuery('id'));

			$item_tnc_l=$view->document->add('CompleteLister',null,'terms_and_conditions',['view/qsp/master','terms_and_conditions']);
			$item_tnc_l->setModel($item_m);	

		}

	}

}