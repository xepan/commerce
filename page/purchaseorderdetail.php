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

 class page_purchaseorderdetail extends \xepan\base\Page {
	public $title='Purchase Order Detail';
	public $breadcrumb=['Home'=>'index','Orders'=>'xepan_commerce_purchaseorder','Detail'=>'#', 'New Purchase Order'=>'xepan_commerce_purchaseorderdetail&action=add'];


	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$purchase_odr_dtl = $this->add('xepan\commerce\Model_PurchaseOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
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
							'outsource_party',

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
							'outsource_party_id',
							// 'priority_id',
							// 'payment_gateway_id',
							// 'transaction_reference',
							// 'transaction_response_data',
							'tnc_id'
						];
		
		$dv = $this->add('xepan\commerce\View_QSPAddressJS')->set('');

		$this->app->show_only_stock_effect_customField = true;
		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$purchase_odr_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);
		$view->js(true)->_selector('#shipping-hide')->hide();
		$this->app->show_only_stock_effect_customField = false;

		$view->js('click')->_selector('a.new-qsp')->univ()->location($this->app->url(null,['action'=>'add','document_id'=>false]));

		if($action !='view'){
			$contact_field = $view->document->form->getElement('contact_id');
			$contact_field->model->addCondition('type','Supplier');

			$contact_field->other_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));
			
			// show only purchaseable item
			if($view->document_item instanceof \CRUD && $view->document_item->isEditing()){
				$field_item = $view->document_item->form->getElement('item_id');
				$field_item->model->addCondition('is_purchasable',true);
			}

		}
	}

}