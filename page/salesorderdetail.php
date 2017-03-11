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

	public $breadcrumb=['Home'=>'index','Orders'=>'xepan_commerce_salesorder','Detail'=>'#','New Sales Order'=>'xepan_commerce_salesorderdetail&action=add'];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$document_id=$this->app->stickyGET('document_id');
		$sale_odr_dtl = $this->add('xepan\commerce\Model_SalesOrder')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
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

		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$sale_odr_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);
		
		if($sale_odr_dtl->id){
			$consumable_view = $this->add('xepan\commerce\View_StockAvailibility',['sale_order_id'=>$sale_odr_dtl->id]);
			if($document_id){
				$consumable_view->setModel($sale_odr_dtl->orderItems());
			}
			if($view->document_item)
				$view->document_item->js('reload',$consumable_view->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$consumable_view->name])]));
		}

		$view->js('click')->_selector('a.new-qsp')->univ()->location($this->app->url(null,['action'=>'add','document_id'=>false]));
				
		$vp = $this->add('VirtualPage');
		$vp->set(function($p){
			$order_id = $p->app->stickyGET('order_id');
			$attachments = $p->add('xepan\commerce\Model_QSP_DetailAttachment');
			$attachments->addCondition('qsp_detail_id',$order_id);
			
			$grid = $p->add('xepan\base\Grid',null,null,['view\qsp\attachments']);
			$grid->setModel($attachments);
		});

		$view->on('click','.order-export-attachments',function($js,$data)use($vp){
			return $js->univ()->dialogURL("EXPORT ATTACHMENTS",$this->api->url($vp->getURL(),['order_id'=>$data['id']]));
		});

		if($action !='view'){
			$contact_field = $view->document->form->getElement('contact_id');
			$contact_field->model->addCondition('type','Customer');

			$contact_field->other_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));
		}
	}

}