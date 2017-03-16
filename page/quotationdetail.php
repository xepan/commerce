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

 class page_quotationdetail extends \xepan\base\Page {
	public $title='Quotation Item';

	public $breadcrumb=['Home'=>'index','Quotations'=>'xepan_commerce_quotation','Detail'=>'#','New Quotation'=>'xepan_commerce_quotationdetail&action=add'];
	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
	
		$quotation = $this->add('xepan\commerce\Model_Quotation')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
		$view_field = 	[
							'contact_id',
							'document_no',
							'type',

							'billing_address',
							'billing_city',
							'billing_pincode',
							'billing_contact',
							'billing_email',

							'shipping_address',
							'shipping_city',
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
							'currency'
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
							'tnc_id'
						];
		
		$dv = $this->add('xepan\commerce\View_QSPAddressJS')->set('');

		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$quotation,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);

		$view->js('click')->_selector('a.new-qsp')->univ()->location($this->app->url(null,['action'=>'add','document_id'=>false]));
		
		if($action !='view'){
			$contact_field = $view->document->form->getElement('contact_id');						
			$contact_field->other_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));
		}


		if($action =='edit'){
			//quotation item add only saleable item
			if(isset($view->document_item)){
				$item_model = $view->document_item->form->getElement('item_id')->getModel();
				$item_model
					->addCondition('status','Published')
					->addCondition('is_saleable',true)
					->addCondition('is_template',false)
					;				
			}			
		}
	}

}