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

 class page_salesorderdetail extends \xepan\base\Page {
	public $title='Sales Order Detail';

	public $breadcrumb=['Home'=>'index','Orders'=>'xepan_commerce_salesorder','Detail'=>'#'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$sale_odr_dtl = $this->add('xepan\commerce\Model_SalesOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$view_field = 	[
							'contact_id',
							'document_no',
							'type',

							'billing_landmark',
							'billing_address',
							'billing_city',
							'billing_state',
							'billing_country',
							'billing_pincode',
							'billing_contact',
							'billing_email',
							'shipping_landmark',
							'shipping_address',
							'shipping_city',
							'shipping_state',
							'shipping_country',
							'shipping_pincode',
							'shipping_contact',
							'shipping_email',

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
							
							'billing_landmark',
							'billing_address',
							'billing_city',
							'billing_state',
							'billing_country',
							'billing_pincode',
							'billing_contact',
							'billing_email',
							'shipping_landmark',
							'shipping_address',
							'shipping_city',
							'shipping_state',
							'shipping_country',
							'shipping_pincode',
							'shipping_contact',
							'shipping_email',

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

		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$sale_odr_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);

		$contact_field = $view->document->form->getElement('contact_id');
		$contact_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));

	}

}