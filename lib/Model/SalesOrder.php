<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Approved','InProgress','Canceled','Completed','Dispatched','OnlineUnpaid'];
	public $actions = [
	'Draft'=>['view','edit','delete','submit','manage_attachments'],
	'Submitted'=>['view','edit','delete','approve','manage_attachments','createInvoice','print_document'],
	'Approved'=>['view','edit','delete','inprogress','manage_attachments','createInvoice','print_document'],
	'InProgress'=>['view','edit','delete','cancel','complete','manage_attachments'],
	'Canceled'=>['view','edit','delete','manage_attachments'],
	'Completed'=>['view','edit','delete','manage_attachments','print_document'],
	'OnlineUnpaid'=>['view','edit','delete','inprogress','createInvoice','manage_attachments','print_document']
				// 'Returned'=>['view','edit','delete','manage_attachments']
	];


	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');
		$this->getElement('document_no')->defaultValue($this->newNumber());
		
		$this->addExpression('days_left')->set(function($m,$q){
			$date=$m->add('\xepan\base\xDate');
			$diff = $date->diff(
				date('Y-m-d H:i:s',strtotime($m['created_at'])
					),
				date('Y-m-d H:i:s',strtotime($m['due_date']?$m['due_date']:$this->app->today)),'Days'
				);

			return "'".$diff."'";
		});
	}

	function print_document(){
		$this->print_QSP();
	}

	function page_send($page){
		$this->send_QSP($page);
	}

	function inprogress(){
		$this['status']='InProgress';
		$this->app->employee
		->addActivity("Sales Order no. '".$this['document_no']."' proceed for dispatching", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
		->notifyWhoCan('cancel,complete','InProgress',$this);
		$this->save();
	}

	function cancel(){
		$this['status']='Canceled';
		$this->app->employee
		->addActivity("Sales Order no. '".$this['document_no']."' canceled by customer", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
		->notifyWhoCan('delete','Canceled',$this);
		$this->saveAndUnload();
	}

	function complete(){
		$this['status']='Completed';
		$this->app->employee
		->addActivity("Sales Order no. '".$this['document_no']."' has been successfully dispatched", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
		->notifyWhoCan('edit,delete','Completed',$this);
		$this->saveAndUnload();
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
		->addActivity("Sales Order no. '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
		->notifyWhoCan('approve,createInvoice','Submitted',$this);
		$this->saveAndUnload();
	}

	function page_approve($page){

		$page->add('View_Info')->setElement('H2')->setHTML('Approving Job Card will move this order to approved status and create JobCards to receive in respective FIRST Departments for EACH Item');

		$form = $page->add('Form_Stacked');
		$form->addField('text','comments');
		$form->addSubmit('Approve & Create Jobcards')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$this->approve();
			$this->app->employee
			->addActivity("Sales Order no. ".$this['document_no']."'s Jobcard created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
			->notifyWhoCan('inprogress,manage_attachments,createInvoice','Approved');
			return $page->js()->univ()->closeDialog();
		}
	}

	function approve(){
		$this['status']='Approved';
		$this->save();
		$this->app->hook('sales_order_approved',[$this]);
		return true;
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
		if($inv->loaded()) return $inv;
		return false;
	}

	function page_createInvoice($page){
		$page->add('View')->set('Order No: '.$this['document_no']);
		if(!$this->loaded()){
			$page->add('View_Error')->set("model must loaded");
			return;
		}

		$form = $page->add('Form');
		$form->addSubmit('Create Invoice');
		if($form->isSubmitted()){

			$new_invoice = $this->createInvoice();

			return $form->js()->univ()->location($this->api->url('xepan_commerce_salesinvoicedetail',['action'=>'edit','document_id'=>$new_invoice->id]));

		}

	}


	function createInvoice($status='Due'){
		
		if(!$this->loaded())
			throw new \Exception("model must loaded before creating invoice", 1);
		
		$customer = $this->customer();
		
		$tnc_model = $this->add('xepan\commerce\Model_TNC')->addCondition('is_default_for_sale_invoice',true)->tryLoadAny();

		$invoice = $this->add('xepan\commerce\Model_SalesInvoice');

		$invoice['contact_id'] = $customer->id;
		$invoice['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		$invoice['related_qsp_master_id'] = $this->id;
		$invoice['tnc_id'] = $tnc_model?$tnc_model->id:$this['tnc_id'];
		$invoice['tnc_text'] = $tnc_model?$tnc_model['content']:$this['tnc_text'];
		
		$invoice['status'] = $status;
		$invoice['due_date'] = null;
		$invoice['exchange_rate'] = $this['exchange_rate'];

		$invoice['document_no'] = $invoice['document_no'];

		$invoice['billing_address'] = $this['billing_address'];
		$invoice['billing_city'] = $this['billing_city'];
		$invoice['billing_state'] = $this['billing_state'];
		
		$invoice['billing_country'] = $this['billing_country'];
		$invoice['billing_pincode'] = $this['billing_pincode'];
		
		$invoice['shipping_address'] = $this['shipping_address'];
		$invoice['shipping_city'] = $this['shipping_city'];
		$invoice['shipping_state'] = $this['shipping_state'];
		$invoice['shipping_country'] = $this['shipping_country'];
		$invoice['shipping_pincode'] = $this['shipping_pincode'];

		$invoice['discount_amount'] = $this['discount_amount']?:0;
		// $invoice['tax'] = $this['tax_amount'];
		$invoice->save();
		
		//here this is current order
		$ois = $this->orderItems();
		foreach ($ois as $oi) {	
				//todo check all invoice created or not
				// $item,$qty,$price,$shipping_charge,$narration=null,$extra_info=null
			$invoice->addItem(
				$oi->item(),
				$oi['price'],
				$oi['quantity'],
				$oi['sale_amount'],
				$oi['original_amount'],
				$oi['shipping_charge'],
				$oi['shipping_duration'],
				$oi['express_shipping_charge'],
				$oi['express_shipping_duration'],
				$oi['narration'],
				$oi['extra_info'],
				$oi['taxation_id'],
				$oi['tax_percentage']
				);
		}

			// if($status !== 'draft' and $status !== 'submitted'){
			// 	$invoice->createVoucher($salesLedger);
			// }
		// throw new \Exception($ois['price']);
		return $invoice;
	}

	function placeOrderFromCart($billing_detail=array()){

		$customer = $this->add('xepan\commerce\Model_Customer');

		if(!$customer->loadLoggedIn())
			throw new \Exception("you logout or session out try again");

		$express_shipping = $this->app->recall('express_shipping');
		//check if address not then save
		if(!$customer['billing_state_id'] or !$customer['billing_country_id'] or !$customer['shipping_country_id'] or !$customer['shipping_state_id'])
			$customer->updateAddress($billing_detail);

		$this['contact_id'] = $customer->id;
		$this['status'] = "OnlineUnpaid";
		
		$this['billing_address'] = $billing_detail['billing_address'];
		$this['billing_city'] = $billing_detail['billing_city'];
		$this['billing_state'] = $billing_detail['billing_state'];
		$this['billing_country'] = $billing_detail['billing_country'];
		$this['billing_pincode'] = $billing_detail['billing_pincode'];
		
		$this['shipping_address'] = $billing_detail['shipping_address'];
		$this['shipping_city'] = $billing_detail['shipping_city'];
		$this['shipping_state'] = $billing_detail['shipping_state'];
		$this['shipping_country'] = $billing_detail['shipping_country'];
		$this['shipping_pincode'] = $billing_detail['shipping_pincode'];

		$this['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		$this['exchange_rate'] = $this->app->epan->default_currency['value'];
		$this['from'] = "Online";
		
		//Load Default TNC
		$tnc = $this->add('xepan\commerce\Model_TNC')->addCondition('is_default_for_sale_order',true)->setLimit(1)->tryLoadAny();
		$this['tnc_id'] = $tnc->loaded()?$tnc['id']:0;
		$this['tnc_text'] = $tnc['content']?$tnc['content']:"not defined";
		//Sale Order Saved
		$this->save();
		
		$cart_items = $this->add('xepan\commerce\Model_Cart');

		$max_regular_shipping_days = 0;
		$max_express_shipping_days = 0;

		$totals = ["price"=>0,"sales_amount"=>0,"qty"=>0,"shipping_charge"=>0,"express_shipping_charge"=>0];
		foreach ($cart_items as $junk) {
			$totals['price'] += $junk['price'];
			$totals['sales_amount'] += $junk['sales_amount'];
			$totals['qty'] += $junk['qty'];
			$totals['shipping_charge'] += $junk['shipping_charge'];
			$totals['express_shipping_charge'] += $junk['express_shipping_charge'];

			if($junk['shipping_duration_days'] > $max_regular_shipping_days)
				$max_regular_shipping_days = $junk['shipping_duration_days'];
			
			if($junk['express_shipping_duration_days'] > $max_express_shipping_days)
				$max_express_shipping_days = $junk['express_shipping_duration_days'];
		}

		// get epan config used for taxation with shipping or price
		$misc_config = $this->app->epan->config;
		$tax_on_shipping = $misc_config->getConfig('TAX_ON_SHIPPING');
		$tax_on_discounted_amount = $misc_config->getConfig('TAX_ON_DISCOUNTED_PRICE');
		$item_price_and_shipping_inclusive_tax = $misc_config->getConfig('ITEM_PRICE_AND_SHIPPING_INCLUSIVE_TAX');

		foreach ($cart_items as $cart_item) {
			
			$order_details = $this->add('xepan\commerce\Model_QSP_Detail');

			$order_details['item_id'] = $cart_item['item_id'];
			$order_details['qsp_master_id'] = $this->id;
			$order_details['quantity'] = $cart_item['qty'];
			
			$tax_percentage = $cart_item['taxation']['percentage'];

			if($tax_on_discounted_amount){
				$order_details['price']=($cart_item['sales_amount']*100)/(100+$tax_percentage); // reverse with tax
			}else{
				$order_details['price']=($cart_item['sales_amount']*100)/(100+$tax_percentage); // reverse with tax
			}
			
			if($tax_on_shipping){
				$field = 'shipping_charge';
				if($express) $field ='express_shipping_charge';
				$order_details['shipping_charge']=($cart_item[$field]*100)/(100+$tax_percentage); // reverse of tax if tax on shipping
			}else{
				$order_details['shipping_charge']=$cart_item['shipping_charge']; // reverse of tax if tax on shipping
			}

			$order_details['extra_info'] = $cart_item['custom_fields'];
			
			$order_details['taxation_id'] = $cart_item['taxation']['id'];
			$order_details['tax_percentage'] = $tax_percentage;

			$order_details->save();

			// //todo many file_uplod_id
			$file_uplod_id_array = json_decode($cart_item['file_upload_ids']);

			foreach ($file_uplod_id_array as $file_id) {
				$attachments = $this->add("xepan\commerce\Model_QSP_DetailAttachment");
				$attachments['contact_id'] = $customer->id;
				$attachments['qsp_detail_id'] = $order_details->id;
				$attachments['file_id'] = $file_id;
				$attachments->save();
				
			}
		}

		//calculate discount amount
		// $discount_voucher = $this->app->recall('discount_voucher');
		// $this['discount_amount'] = 0;//$discount_amount;

		//calculating max due date of order according to max shipping_date of order item
 		$max_due_date = date("Y-m-d H:i:s", strtotime("+".$max_regular_shipping_days." days", strtotime($this['created_at'])));
		if($express_shipping)
 			$max_due_date = date("Y-m-d H:i:s", strtotime("+".$max_express_shipping_days." days", strtotime($this['created_at'])));

 		$this['due_date'] = $max_due_date;
 		$this->save();

 		// actually checkout process is change so invoice create after order verified by customer in checkout step 3
		// $this->createInvoice('Due');
		return $this;
	}

	function addOrdItem($item,$qty,$price,$sale_amount,$original_amount,$shipping_charge,$shipping_duration,$express_shipping_charge=null,$express_shipping_duration=null,$narration=null,$extra_info=null,$taxation_id=null,$tax_percentage=null){
		if(!$this->loaded())
			throw new \Exception("SalesOrder must loaded", 1);

		// throw new \Exception($this->id);

		if(!$taxation_id and $tax_percentage){
			$tax = $item->applyTax();
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

}
