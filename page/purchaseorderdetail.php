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
	public $title='Purchase Order Detail';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$purchase_odr_dtl = $this->add('xepan\commerce\Model_PurchaseOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$view_field = 	[
							'contact_id',
							'document_no',
							'billing_landmark',
							'billing_address',
							'billing_city',
							'billing_state',
							'billing_country',
							'billing_pincode',
							'billing_tel',
							'billing_email',
							'shipping_landmark',
							'shipping_address',
							'shipping_city',
							'shipping_state',
							'shipping_country',
							'shipping_pincode',
							'shipping_tel',
							'shipping_email',

							'gross_amount',
							'discount_amount',
							'net_amount',
							'delivery_date',
							'priority_id',
							'narration',
							'exchange_rate',
							'payment_gateway_id',
							'transaction_reference',
							'transaction_response_data',
						];
		$form_field	=[
					'contact_id',
					'document_no',
					// 'billing_landmark',
					// 'billing_address',
					// 'billing_city',
					// 'billing_state',
					// 'billing_country',
					// 'billing_pincode',
					// 'billing_tel',
					// 'billing_email',
					// 'shipping_landmark',
					// 'shipping_address',
					// 'shipping_city',
					// 'shipping_state',
					// 'shipping_country',
					// 'shipping_pincode',
					// 'shipping_tel',
					// 'shipping_email',

					'discount_amount',
					'delivery_date',
					'narration',
					'exchange_rate',
					// 'priority_id',
					// 'payment_gateway_id',
					// 'transaction_reference',
					// 'transaction_response_data',
				];
				
		$this->add('xepan\commerce\View_QSP',['qsp_model'=>$purchase_odr_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);

	}

}