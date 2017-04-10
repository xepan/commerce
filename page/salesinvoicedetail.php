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

class page_salesinvoicedetail extends \xepan\base\Page {
	public $title='Sales Invoice Detail';

	public $breadcrumb=['Home'=>'index','Invoices'=>'xepan_commerce_salesinvoice','Detail'=>'#', 'New Sales Invoice'=>'xepan_commerce_salesinvoicedetail&action=add'];
	public $serial_nos = [];

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		
		$sale_inv_dtl = $this->add('xepan\commerce\Model_SalesInvoice')->tryLoadBy('id',$this->api->stickyGET('document_id'));
		
							$view_field = 	[
							'contact_id',
							'document_no',
							'type',

							'billing_address',
							'billing_country',
							'billing_pincode',
							'shipping_address',
							'shipping_city',
							'shipping_pincode',
							'shipping_tel',

							'gross_amount',
							'discount_amount',
							'net_amount',
							'delivery_date',
							'tnc_text',
							'narration',
							'exchange_rate',
							'currency',
							'nominal_id',
							'created_at'
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
							'tnc_id',
							'nominal_id'
							];
		
		$dv = $this->add('xepan\commerce\View_QSPAddressJS')->set('');

		$view = $this->add('xepan\commerce\View_QSP',['qsp_model'=>$sale_inv_dtl,'qsp_view_field'=>$view_field,'qsp_form_field'=>$form_field]);
		
		
		if($action !='view'){
			$contact_field = $view->document->form->getElement('contact_id');
			$contact_field->model->addCondition('type','Customer');

			$contact_field->other_field->js('change',$dv->js()->reload(['changed_contact_id'=>$contact_field->js()->val()]));
		}

		if($action=='edit' && !$view->document_item->isEditing()){
			$view->app->addHook('post-submit',function($f)use($sale_inv_dtl){
				if($_POST){
					$sale_inv_dtl->addHook('afterSave',function($m){
						$m->updateTransaction();

					});
				}
			});
		}
		
		$view->js('click')->_selector('a.new-qsp')->univ()->location($this->app->url(null,['action'=>'add','document_id'=>false]));
	}
}


	
		// serial number
		// if($view->document_item->isEditing()){
		// 	$form = $view->document_item->form;

			// if($form->isSubmitted()){

			// 	$temp_item = $this->add('xepan\commerce\Model_Item')->load($form['item_id']);
	  			
	  // 			if($temp_item['is_serializable']){
			// 		$code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$form['serial_nos'])));
			//         $serial_no_array = [];
			//         if(strlen($code))
			//         	$serial_no_array = explode("\n",$code);
			//         if($form['quantity'] != count($serial_no_array))
			//             $form->error('serial_nos','count of serial nos must be equal to quantity '.$form['quantity']. " = ".count($serial_no_array));
					
			// 		// check all serial no is exist or not
			// 		$not_found_no = [];
			// 		foreach ($serial_no_array as $key => $value) {
			// 			$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
			// 			$serial_model->addCondition('item_id',$temp_item->id);
			// 			$serial_model->addCondition('serial_no',$value);
			// 			$serial_model->addCondition('is_available',true);
			// 			$serial_model->tryLoadAny();
			// 			if(!$serial_model->loaded())
			// 				$not_found_no[] = $value;
			// 		}

			// 		if(count($not_found_no))
			//             $form->error('serial_nos','some of serial no are not available '. implode(", ", $not_found_no) );

			//         // insert serial Number
			//      	$this->app->memorize('serial_no_array',$serial_no_array);

			//      	echo "serila no memo rize";
			//      	echo "<pre>";
			//      	print_r($serial_no_array);
			//      	echo "</pre>";
			// 	}
			// }
		// }

		// if(isset($view->document_item)){
		// 	// item specific terms and conditions
		// 	$item_m=$this->add('xepan\commerce\Model_Item');
		// 	$detail_j=$item_m->join('qsp_detail.item_id');
		// 	$detail_j->addField('detail_id','id');
		// 	$item_m->addCondition('detail_id','in',$view->document_item->model->fieldQuery('id'));
		// 	$item_m->addCondition('terms_and_conditions','<>',null);

		// 	$item_tnc_l=$view->document->add('CompleteLister',null,'terms_and_conditions',['view/qsp/master','terms_and_conditions']);
		// 	$item_tnc_l->setModel($item_m);
		// 	$item_tnc_l->addHook('formatRow',function($l){
		// 		    $l->current_row_html['terms_and_conditions']  = $l->model['terms_and_conditions'];
		// 	});
			
		// }						