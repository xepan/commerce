<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Approved','InProgress','Canceled','UnderDispatch','Completed','Dispatched','OnlineUnpaid'];
	public $actions = [

	'Draft'=>['view','submit','other_info','cancel','edit','delete','manage_attachments','communication'],
	'Submitted'=>['view','approve','redesign','cancel','manage_attachments','print_document','other_info','edit','delete','communication'],
	'Approved'=>['view','inprogress','cancel','createInvoice','print_document','send','other_info','send_to_dispatch','manage_attachments','edit','delete','communication'],
	'InProgress'=>['view','other_info','cancel','edit','delete','complete','manage_attachments','send','communication'],
	'Canceled'=>['view','other_info','edit','delete','redraft','manage_attachments','communication'],
	'UnderDispatch'=>['view','complete','send','other_info','print_document','cancel','edit','delete','manage_attachments','communication'],
	'Completed'=>['view','createInvoice','print_document','send','send_to_dispatch','other_info','cancel','edit','delete','manage_attachments','communication'],
	'OnlineUnpaid'=>['view','other_info','cancel','edit','delete','approve','createInvoice','manage_attachments','print_document','send','communication'],
	'Redesign'=>['view','other_info','cancel','edit','delete','submit','manage_attachments','communication']
	// 'Returned'=>['view','edit','delete','manage_attachments']
	];

	public $addOtherInfo = true;
	public $document_type = 'SalesOrder';

	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');
		$this->getElement('document_no');//->defaultValue($this->newNumber());
		
		$this->addExpression('days_left')->set(function($m,$q){
			$date=$m->add('\xepan\base\xDate');
			$diff = $date->diff(
				date('Y-m-d H:i:s',strtotime($m['created_at'])
					),
				date('Y-m-d H:i:s',strtotime($m['due_date']?$m['due_date']:$this->app->today)),'Days'
				);

			return "'".$diff."'";
		});

		// $this->is([
		// 	'document_no|required|number'
		// 	]);
	}

	function print_document(){
		$this->print_QSP();
	}

	function page_send($page){
		$this->send_QSP($page,$this);
	}

	function redraft(){
		$this['status']='Draft';
		$this->app->employee
		->addActivity("Sales Order No : '".$this['document_no']."' redraft", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this->id."")
		->notifyWhoCan('submit','Draft',$this);
		$this->save();
	}	

	function inprogress(){
		$this['status']='InProgress';
		$this->app->employee
		->addActivity("Sales Order No : '".$this['document_no']."' is inprogress", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this->id."")
		->notifyWhoCan('cancel,complete','InProgress',$this);
		$this->save();
	}


	function cancel($reason=null,$narration=null){
		$this['status']='Canceled';
		if($reason)
			$this['cancel_reason'] = $reason;
		if($narration)
			$this['cancel_narration'] = $narration;

		$this->app->employee
			->addActivity("Sales Order No : '".$this['document_no']."' canceled by customer", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this->id."")
			->notifyWhoCan('delete','Canceled',$this);
		$this->save();
		return true;
	}

	function complete(){
		$this['status']='Completed';
		$this->app->employee
		->addActivity("Sales Order No : '".$this['document_no']."' has been successfully dispatched", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this->id."")
		->notifyWhoCan('edit,delete','Completed',$this);
		$this->save();
	}

	function redesign(){
		$this['status']='Redesign';
		$this->app->employee
		->addActivity("sale order No : '".$this['document_no']."' proceed for redesign", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_quotationdetail&document_id=".$this->id."")
		->notifyWhoCan('submit,approve','Redesign',$this);
		$this->save();
	}
	
	function isCompleted(){
		if(!$this->loaded())
			throw new \Exception("model must loaded", 1);
		
		if($this['status'] == "Completed")
			return true;

		return false;
	}

	function submit(){
		$this['status']='Submitted';
		$this->app->employee
		->addActivity("Sales Order No : '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this->id."")
		->notifyWhoCan('approve,createInvoice','Submitted',$this);
		$this->save();
	}

	function page_approve($page){

		$col = $page->add('Columns');
		$col1 = $col->addColumn('3');
		$col2 = $col->addColumn('9');
		
		$col1->add('View_Info')->setElement('div')->setHTML('<ul><li>Approving JobCard will move this order to approved status</li> <li> it auto creates jobcrad in first production department respective to each order item</li>');
		$form = $col1->add('Form_Stacked');
		$form->addField('text','comments');
		$form->addSubmit('Approve & Create Jobcards')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$this->approve();
			$jobcard = $this->add('xepan\production\Model_Jobcard');
			$jobcard->addCondition('order_no',$this['document_no']);
			$jobcard->tryLoadAny();
			$this->app->employee
			->addActivity("Sales Order No : ".$this['document_no']."' Approved, And Its Jobcard No : '".$jobcard->id."' successfully created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesorderdetail&document_id=".$this->id."")
			->notifyWhoCan('inprogress,manage_attachments,createInvoice','Approved');
			return $page->js()->univ()->closeDialog();
		}

		$col2->add('View')->setElement('h3')->set('Sale Order: '.$this['serial']." ".$this['document_no']." of ".$this['contact']);
		$stock_view = $col2->add('xepan\commerce\View_StockAvailibility',['sale_order_id'=>$this->id]);
	 	$stock_view->setModel($this->orderItems());
	}

	function approve(){
		if(!$this['document_no'] || $this['document_no']=='-') $this['document_no']=$this->newNumber();
		$this['status']='Approved';
		$this->save();
		$this->bookConsumptions();
		$this->app->hook('sales_order_approved',[$this]);
		return true;
	}

	function bookConsumptions(){
		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
		$warehouse->tryLoadAny();
		
		foreach ($this->orderItems() as $oi) {
			$item = $this->add('xepan\commerce\Model_Item')->load($oi['item_id']);
			$cf_info = json_decode($oi['extra_info'],true);
			$cf_info = $item->convertCustomFieldToKey($cf_info);

			/*Order Item New Transaction*/
			$transaction = $warehouse->newTransaction($this->id,null,$warehouse->id,'Consumption_Booked',null);
			$transaction->addItem($oi->id,$oi['item_id'],$oi['quantity'],null,$cf_info,'ToReceived',$item['qty_unit_id'],$oi['qty_unit_id']);

			$custom_fields = $item->getConsumption($oi['quantity'],json_decode($oi['extra_info'],true),$oi['item_id'],$oi['qty_unit_id']);
			unset($custom_fields['total']);
			$cf_key  = $item->convertCustomFieldToKey(json_decode($oi['extra_info'],true));
			
			/*Order Item Production Department*/
			foreach ($custom_fields as $department_id => $value) {
				unset($value['department_name']);
				
				/*Consumption Item New Transaction*/
				$transaction = $warehouse->newTransaction($this->id,null,$warehouse->id,'Consumption_Booked',$department_id);

	// 			/*Department Consumption Item*/
				foreach ($value as $item_id => $cf_array) {
					foreach ($cf_array as $cf_k => $key_value) {
						$transaction->addItem($oi->id,$item_id,$key_value['qty'],null,$cf_k,'ToReceived',null,null,false);
					}
					
				}
			}
		}
		
	}



	function orderItems(){
		if(!$this->loaded())
			throw new \Exception("loaded sale order required");

		return $order_details = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
	}

	function customer(){
		return $this->ref('contact_id');
	}

	function invoice(){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded, SaleOrder");
		
		$inv = $this->add('xepan\commerce\Model_SalesInvoice')
		->addCondition('related_qsp_master_id',$this->id);

		$inv->tryLoadAny();
		if($inv->loaded()){
			return $inv;
		}else {
			return $this->createInvoice();
		}
		
		return false;
	}

	function page_createInvoice($page){
		$page->add('View')->set('Order No: '.$this['document_no']);
		if(!$this->loaded()){
			$page->add('View_Error')->set("model must loaded");
			return;
		}

		$inv = $this->invoice();
		if(!$inv){
			$page->add('View')->set("You have successfully created invoice of this order, you can edit too ")->addClass('project-box-header green-bg well-sm')->setstyle('color','white');
			$new_invoice = $this->createInvoice();
			$form = $page->add('Form');
			$form->addSubmit('Edit Invoice');
			if($form->isSubmitted()){
				// return $form->js()->univ()->frameURL('Sales Invoice Details',[$this->api->url('xepan_commerce_salesinvoicedetail',['action'=>'view','document_id'=>$new_invoice->id])]);
				return $form->js()->univ()->location($this->api->url('xepan_commerce_salesinvoicedetail',['action'=>'edit','document_id'=>$new_invoice->id]));
			}
			$page->add('xepan\commerce\View_QSP',['qsp_model'=>$new_invoice]);
		}else{
			$page->add('View_Info')->set("You have created invoice of this order")->addClass('project-box-header green-bg well-sm')->setstyle('color','white');
			$form = $page->add('Form');
			$form->addSubmit('Edit Invoice');
				if($form->isSubmitted()){
					return $form->js()->univ()->location($this->api->url('xepan_commerce_salesinvoicedetail',['action'=>'edit','document_id'=>$inv->id]));
				}

			$page->add('xepan\commerce\View_QSP',['qsp_model'=>$inv]);
		}
	}


	function createInvoice($status='Due'){
		if(!$this->loaded())
			throw new \Exception("model must loaded before creating invoice", 1);
		
		$customer = $this->customer();
		
		$tnc_model = $this->add('xepan\commerce\Model_TNC')->addCondition('is_default_for_sale_invoice',true)->tryLoadAny();

		$invoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$invoice->addCondition('related_qsp_master_id',$this->id);
		$invoice->tryLoadAny();

		if(!$invoice->loaded() && $status == 'Due'){
			$invoice['document_no'] = $invoice->newNumber();
		}else{
			$invoice['document_no'] = '-';
		}

		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		$master_data = $this->get();
		$detail_data = [];
		
		$ois = $this->orderItems();
		foreach ($ois as $oi) {
			$detail_data[] = $oi->get();
		}

		$master_data['serial'] = $qsp_config['sale_invoice_serial'];
		$master_data['related_qsp_master_id'] = $this->id;
		$master_data['status'] = $status;
		$due_date = $this->app->now;
		if($master_data['due_date'] > $this['created_at']){
			$due_date = $this['due_date'];
		}
		$master_data['due_date'] = $due_date;
		$master_data['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');

		unset($master_data['document_no']);
		unset($master_data['created_at']);
		
		$qsp = $this->add('xepan\commerce\Model_QSP_Master');
		
		$qsp_master = $qsp->createQSP($master_data,$detail_data,'SalesInvoice');
		return $this->add('xepan\commerce\Model_SalesInvoice')->load($qsp_master['master_detail']['id'])->save();
		
		// $invoice['contact_id'] = $customer->id;
		// $invoice['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		// // $invoice['related_qsp_master_id'] = $this->id;
		// $invoice['tnc_id'] = $tnc_model?$tnc_model->id:$this['tnc_id'];
		// $invoice['tnc_text'] = $tnc_model?$tnc_model['content']:$this['tnc_text'];
		


		// $invoice['due_date'] = $due_date;
		// $invoice['exchange_rate'] = $this['exchange_rate'];
		// // $invoice['document_no'] = $invoice['document_no'];

		// $invoice['billing_name'] = $this['billing_name'];
		// $invoice['billing_address'] = $this['billing_address'];
		// $invoice['billing_city'] = $this['billing_city'];
		// $invoice['billing_state_id'] = $this['billing_state_id'];
		
		// $invoice['billing_country_id'] = $this['billing_country_id'];
		// $invoice['billing_pincode'] = $this['billing_pincode'];
		
		// $invoice['shipping_name'] = $this['shipping_name'];
		// $invoice['shipping_address'] = $this['shipping_address'];
		// $invoice['shipping_city'] = $this['shipping_city'];
		// $invoice['shipping_state_id'] = $this['shipping_state_id'];
		// $invoice['shipping_country_id'] = $this['shipping_country_id'];
		// $invoice['shipping_pincode'] = $this['shipping_pincode'];
		
		// $invoice['is_shipping_inclusive_tax'] = $this['is_shipping_inclusive_tax'];
		// $invoice['from'] = $this['from'];

		// $invoice['discount_amount'] = $this['discount_amount']?:0.00;
		// $invoice['is_express_shipping'] = $this['is_express_shipping']?:0.00;		
		// $invoice->save();
		
		// //here this is current order
		// $ois = $this->orderItems();
		// foreach ($ois as $oi) {	
		// 		//todo check all invoice created or not
		// 	$invoice->addItem(
		// 		$oi->item(),
		// 		$oi['quantity'],
		// 		$oi['price'],
		// 		$oi['sale_amount'],
		// 		$oi['original_amount'],
		// 		$oi['shipping_charge'],
		// 		$oi['shipping_duration'],
		// 		$oi['express_shipping_charge'],
		// 		$oi['express_shipping_duration'],
		// 		$oi['narration'],
		// 		$oi['extra_info'],
		// 		$oi['taxation_id'],
		// 		$oi['tax_percentage'],
		// 		$oi['qty_unit_id']
		// 		);
		// }

		// $invoice->reload();
		// $invoice->updateTransaction();
		// return $invoice;
	}

	function placeOrderFromCart($billing_detail=array(),$send_order=true){

		$customer = $this->add('xepan\commerce\Model_Customer');

		if(!$customer->loadLoggedIn("Customer"))
			throw new \Exception("you logout or session out try again");

		$express_shipping = $this->app->recall('express_shipping');
		//check if address not then save
		// if(!$customer['billing_state_id'] or !$customer['billing_country_id'] or !$customer['shipping_country_id'] or !$customer['shipping_state_id'])
		$customer->updateAddress($billing_detail);

		$this['contact_id'] = $customer->id;
		$this['status'] = "OnlineUnpaid";
		
		$this['billing_name'] = $billing_detail['billing_name'];
		$this['billing_address'] = $billing_detail['billing_address'];
		$this['billing_city'] = $billing_detail['billing_city'];
		$this['billing_state_id'] = $billing_detail['billing_state_id'];
		$this['billing_country_id'] = $billing_detail['billing_country_id'];
		$this['billing_pincode'] = $billing_detail['billing_pincode'];
		
		$this['shipping_name'] = $billing_detail['shipping_name'];
		$this['shipping_address'] = $billing_detail['shipping_address'];
		$this['shipping_city'] = $billing_detail['shipping_city'];
		$this['shipping_state_id'] = $billing_detail['shipping_state_id'];
		$this['shipping_country_id'] = $billing_detail['shipping_country_id'];
		$this['shipping_pincode'] = $billing_detail['shipping_pincode'];

		$this['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		$this['exchange_rate'] = $this->app->epan->default_currency['value'];
		$this['from'] = "Online";
		
		//Load Default TNC
		$tnc = $this->add('xepan\commerce\Model_TNC')->addCondition('is_default_for_sale_order',true)->setLimit(1)->tryLoadAny();
		$this['tnc_id'] = $tnc->loaded()?$tnc['id']:0;
		$this['tnc_text'] = $tnc['content']?$tnc['content']:"not defined";
			

		$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'tax_on_shipping'=>'checkbox'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$misc_config->tryLoadAny();		

		$tax_on_shipping = $misc_config['tax_on_shipping'];
		$this['is_shipping_inclusive_tax'] = $tax_on_shipping;
		$this->populateSerialNo();
		$this['document_no'] = $this->newNumber();
		//Sale Order Saved
		$this->save();
		
		$cart_items = $this->add('xepan\commerce\Model_Cart');

		$max_regular_shipping_days = 0;
		$max_express_shipping_days = 0;

		foreach ($cart_items as $cart_item) {
			// calculating max shipping day
			if($cart_item['shipping_duration_days'] > $max_regular_shipping_days)
				$max_regular_shipping_days = $cart_item['shipping_duration_days'];
			
			if($cart_item['express_shipping_duration_days'] > $max_express_shipping_days)
				$max_express_shipping_days = $cart_item['express_shipping_duration_days'];


			$order_details = $this->add('xepan\commerce\Model_QSP_Detail');

			$order_details['item_id'] = $cart_item['item_id'];
			$order_details['qsp_master_id'] = $this->id;
			$order_details['quantity'] = $cart_item['qty'];
			$order_details['item_template_design_id'] = $cart_item['item_member_design_id'];
			
			$shipping_field = 'raw_shipping_charge';
			$shipping_discount_field = 'row_discount_shipping';
			$shipping_discount_field = 'row_discount_shipping';
			if($cart_items->is_express_shipping) {
				$shipping_field ='raw_express_shipping_charge';
				$shipping_discount_field = 'row_discount_shipping_express';
			}

			$order_details['shipping_charge']= $cart_item[$shipping_field] - $cart_item[$shipping_discount_field];
			$order_details['price'] = $cart_item['discounted_raw_amount'] / $cart_item['qty'];
			$order_details['extra_info'] = $cart_item['custom_fields'];
			$order_details['taxation_id'] = $cart_item['taxation_id'];	
			$order_details['tax_percentage'] = $cart_item['tax_percentage'];

			// // add item_qty_unit_id
			// $item_model = $this->add('xepan\commerce\Model_Item')->load($cart_item['item_id']);
			$order_details['qty_unit_id'] = $cart_item['qty_unit_id'];

			$order_details->save();

			// save order id in item template desig  id
			if($order_details['item_template_design_id']){
				$td = $this->add('xepan\commerce\Model_Item_Template_Design')->tryLoad($order_details['item_template_design_id']);
				if($td->loaded()){
					$td['order_id'] = $this->id;
					$td->save();
				}
			}

			// //todo many file_uplod_id
			$file_uplod_id_array = json_decode($cart_item['file_upload_ids'],true)?:[];

			foreach ($file_uplod_id_array as $file_id) {
				if(!$file_id) continue;
				
				$attachments = $this->add("xepan\commerce\Model_QSP_DetailAttachment");
				$attachments['contact_id'] = $customer->id;
				$attachments['qsp_detail_id'] = $order_details->id;
				$attachments['file_id'] = $file_id;
				$attachments->save();
				
			}
		}

		//calculating max due date of order according to max shipping_date of order item
 		$max_due_date = date("Y-m-d H:i:s", strtotime("+".$max_regular_shipping_days." days", strtotime($this['created_at'])));
		if($express_shipping)
 			$max_due_date = date("Y-m-d H:i:s", strtotime("+".$max_express_shipping_days." days", strtotime($this['created_at'])));

 		
		$this['is_express_shipping'] = $cart_items->is_express_shipping;
 		
 		if(date('Y',strtotime($max_due_date)) == "1970")
	 		$this['due_date'] = $this->app->now;
 		else		
	 		$this['due_date'] = $max_due_date;
 		
 		$this->save();

 		// actually checkout process is change so invoice create after order verified by customer in checkout step 3
		// $this->createInvoice('Due');
		
		if($send_order && !$this->app->getConfig('test_mode',false)){
			$config_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'from_email'=>'Dropdown',
								'subject'=>'line',
								'body'=>'xepan\base\RichText',
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'SALESORDER_LAYOUT',
						'application'=>'commerce'
				]);
			$config_m->tryLoadAny();

			$from_email = $config_m['from_email'];
			$to_emails = str_replace("<br/>", ",",$this->ref('contact_id')->get('emails_str'));
			$subject = $config_m['subject'];
			$body = $config_m['body'];
			try{
				$this->app->muteACL = true;
				$this->send($from_email,$to_emails,null,null,$subject,$body);
			}catch(\Exception $e){

			}
		}

		return $this;
	}

	function calculateDiscountedPrice($cart_item_sales_amount,$cart_item_shipping_amount,$tax_percentage,$cart_item_qty,$discount_percentage, $discount_on){
		// get epan config used for taxation with shipping or price
		$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'tax_on_shipping'=>'checkbox',
							'tax_on_discounted_price'=>'checkbox'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$misc_config->tryLoadAny();

		$tax_on_shipping = $misc_config['tax_on_shipping'];
		$tax_on_discounted_amount = $misc_config['tax_on_discounted_price'];

		if(!$tax_percentage or !$tax_on_discounted_amount or !in_array($discount_on,['gross','price'])) 
			return $cart_item_sales_amount/ $cart_item_qty;

		$price_without_discount = ($cart_item_sales_amount/ $cart_item_qty);
		
		if($tax_on_shipping)
			return $price_without_discount - ($price_without_discount*$discount_percentage/100);

		return "11";
	}

	function calculateDiscountedShipping($cart_item_shipping_amount,$cart_item_sales_amount,$tax_percentage,$cart_item_qty,$discount_percentage, $discount_on){
		// get epan config used for taxation with shipping or price
		$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'tax_on_shipping'=>'checkbox',
							'tax_on_discounted_price'=>'checkbox'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$misc_config->tryLoadAny();

		$tax_on_shipping = $misc_config['tax_on_shipping'];
		$tax_on_discounted_amount = $misc_config['tax_on_discounted_price'];

		if(!$tax_percentage or !$tax_on_discounted_amount or !in_array($discount_on,['gross','shipping'])) 
			return $cart_item_shipping_amount;

		if($tax_on_shipping) 
			return $cart_item_shipping_amount - ($cart_item_shipping_amount*$discount_percentage/100);

		return "22";
	}

	function addOrdItem($item,$qty,$price,$sale_amount,$original_amount,$shipping_charge,$shipping_duration,$express_shipping_charge=null,$express_shipping_duration=null,$narration=null,$extra_info=null,$taxation_id=null,$tax_percentage=null,$qty_unit_id){
		if(!$this->loaded())
			throw new \Exception("SalesOrder must loaded", 1);

		if(!$taxation_id and $tax_percentage){
			$tax = $item->applicableTaxation();
			$taxation_id = $tax['taxation_id'];
			$tax_percentage = $tax['tax_percentage'];
		}

		$or_item = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
		$or_item['item_id'] = $item->id;
		$or_item['qsp_master_id'] = $this->id;
		$or_item['quantity'] = $qty;
		$or_item['price'] = $price;
		$or_item['shipping_charge'] = $shipping_charge;
		$or_item['shipping_duration'] = $shipping_duration;
		$or_item['sale_amount'] = $sale_amount;
		$or_item['original_amount'] = $original_amount;
		$or_item['shipping_duration'] = $shipping_duration;
		$or_item['express_shipping_charge'] = $express_shipping_charge;
		$or_item['express_shipping_duration'] = $express_shipping_duration;
		$or_item['narration'] = $narration;
		$or_item['extra_info'] = $extra_info;
		$or_item['taxation_id'] = $taxation_id;
		$or_item['tax_percentage'] = $tax_percentage;
		$or_item['qty_unit_id'] = $qty_unit_id;
		$or_item->save();

	}

	function quotationRequest(){
		if(!$this->loaded())
			throw new \Exception("sale order must loaded", 1);
		if(!$this['related_qsp_master_id'])
			throw new \Exception("Related  qutation not found", 1);
		
		$quotation = $this->add('xepan\commerce\Model_Quotation')->tryLoad($this['related_qsp_master_id']);

		if(!$quotation->loaded())
			throw new \Exception("Related order not found", 1);			

		return $quotation;
	}

	function page_send_to_dispatch($page){
        $warehouse = $page->add('xepan\commerce\Model_Store_Warehouse');
		$form = $page->add('Form');
		$warehouse_f = $form->addField('DropDown','send_to_dispatch_warehouse')->validate('required');
    	$warehouse_f->setModel($warehouse);
    	$warehouse_f->setEmptyText('Please Select Dispatch Warehouse');
    	$form->addSubmit('Send To Dispatch');

    	if($form->isSubmitted()){
    		if(!$this->orderItems()->count()->getOne())
    			$form->js()->univ()->errorMessage('Order doesn\'t have any')->execute();

			$this->send_to_dispatch($form['send_to_dispatch_warehouse']);
			return $form->js()->univ()->successMessage('Order '.$this['name'].' Send To Dispatch Warehouse Successfully')->closeDialog();
		}
	}

	function send_to_dispatch($warehouse_id){

		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')
				->load($warehouse_id);
		$transaction = $warehouse->newTransaction($this['id'],null,$this['contact_id'],'Store_DispatchRequest');
		foreach ($this->orderItems() as $oi) {
			$transaction->addItem($oi['id'],$oi['item_id'],$oi['quantity'],null,$oi->convertCustomFieldToKey(json_decode($oi['extra_info'],true)),'ToReceived',$oi['item_qty_unit_id'],$oi['qty_unit_id']);
		}

		$this['status'] = 'UnderDispatch';
		$this->saveAndUnload();
	    return true;
	}
}
