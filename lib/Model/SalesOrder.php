<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','InProgress','Canceled','Completed','OnlineUnpaid'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','approve','manage_attachments','createInvoice'],
				'Approved'=>['view','edit','delete','inprogress','manage_attachments','createInvoice'],
				'InProgress'=>['view','edit','delete','cancel','complete','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments'],
				'OnlineUnpaid'=>['view','edit','delete','inprogress','createInvoice','manage_attachments']
				// 'Returned'=>['view','edit','delete','manage_attachments']
				];


	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');
		
		$this->addExpression('days_left')->set(function($m,$q){
			$date=$m->add('\xepan\base\xDate');
			$diff = $date->diff(
						date('Y-m-d H:i:s',strtotime($m['created_at'])
							),
						date('Y-m-d H:i:s',strtotime($m['due_date'])),'Days'
					);

			return "'".$diff."'";
		});
	}

	function inprogress(){
		$this['status']='InProgress';
        $this->app->employee
            ->addActivity("InProgress QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('cancel','Approved');
        $this->saveAndUnload();
    }

    function cancel(){
		$this['status']='Canceled';
        $this->app->employee
            ->addActivity("Canceled QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();
    }

    function complete(){
		$this['status']='Completed';
        $this->app->employee
            ->addActivity("Completed QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();
    }

	function page_approve($page){

		$page->add('View_Info')->setElement('H2')->setHTML('Approving Job Card will move this order to approved status and create JobCards to receive in respective FIRST Departments for EACH Item');

		$form = $page->add('Form_Stacked');
		$form->addField('text','comments');
		$form->addSubmit('Approve & Create Jobcards');

		if($form->isSubmitted()){
			$this->approve();
			
			$this['status']='InProgress';
        	$this->app->employee
            	->addActivity("SaleOrder Jobcard created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            	->notifyWhoCan('','InProgress');
            $this->saveAndUnload();
            return true;
		}
		return false;
	}

	function approve(){
		$this->app->hook('sales_order_approved',[$this]);
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
		if(!$this->loaded());
			throw new \Exception("Model Must Loaded, SaleOrder");
			
		$inv = $this->add('xepan\commerce\Model_SalesInvoice')
					->addCondition('related_qsp_master_id',$this->id);

		$inv->tryLoadAny();
		if($inv->loaded()) return $inv;
		return false;
	}

	function page_createInvoice($page){
		$page->add('View')->set('Order No: '.$this['tax']);
		if(!$this->loaded()){
			$page->add('View_Error')->set("model must loaded");
			return;
		}

		$form = $page->add('Form');
		$form->addSubmit('create Invoice');
		if($form->isSubmitted()){

			$this->createInvoice();

			$form->js()->univ()->successMessage('invoice craeted')->execute();
		}


	}


	function createInvoice($status='Due',$items_array=[],$amount=0,$discount=0,$shipping_charge=0,$narration=null){
		if(!$this->loaded())
			throw new \Exception("model must loaded before creating invoice", 1);
		
		$customer=$this->customer();
		
		$invoice = $this->add('xepan\commerce\Model_SalesInvoice');

		// $invoice['sales_order_id'] = $order->id;
		$invoice['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		$invoice['related_qsp_master_id'] = $this->id;
		$invoice['contact_id'] = $customer->id;
		$invoice['status'] = $status;
		$invoice['due_date'] = date('Y-m-d');
		$invoice['exchange_rate'] = $this['exchange_rate'];
		$invoice['document_no'] =rand(1000,9999) ;


		$invoice['billing_address'] = $this['billing_address'];
		$invoice['billing_city'] = $this['billing_city'];
		$invoice['billing_state'] = $this['billing_state'];
		$invoice['billing_country'] = $this['billing_country'];
		$invoice['billing_pincode'] = $this['billing_pincode'];
		$invoice['billing_contact'] = $this['billing_contact'];
		$invoice['billing_email'] = $this['billing_email'];
		
		$invoice['shipping_address'] = $this['shipping_address'];
		$invoice['shipping_city'] = $this['shipping_city'];
		$invoice['shipping_state'] = $this['shipping_state'];
		$invoice['shipping_country'] = $this['shipping_country'];
		$invoice['shipping_pincode'] = $this['shipping_pincode'];
		$invoice['shipping_contact'] = $this['shipping_contact'];
		$invoice['shipping_email'] = $this['shipping_email'];

		$invoice['discount_amount'] = $this['discount_amount']?:0;
		// $invoice['tax'] = $this['tax_amount'];
		$invoice['tnc_id'] = $this['tnc_id'];
		$invoice['tnc_text'] = $this['tnc_text']?$this['tnc_text']:"not defined";
		$invoice->save();
			
			//here this is current order
			$ois = $this->orderItems();
			foreach ($ois as $oi) {	
				//todo check all invoice created or not
				// $item,$qty,$price,$shipping_charge,$narration=null,$extra_info=null
				$invoice->addItem(
						$oi->item(),
						$oi['quantity'],
						$oi['price'],
						$oi['shipping_charge'],
						$oi['narration'],
						$oi['extra_info'],
						$oi['taxation_id'],
						$oi['tax_percentage']
					);
			}

			// if($status !== 'draft' and $status !== 'submitted'){
			// 	$invoice->createVoucher($salesLedger);
			// }
		return $invoice;
	}

	function placeOrderFromCart($billing_detail=array()){
		
		$customer = $this->add('xepan\commerce\Model_Customer');
		$customer->loadLoggedIn();

		$this['contact_id'] = $customer->id?:1;
		$this['status'] = "OnlineUnpaid";
		
		$this['billing_address'] = $billing_detail['billing_address'];
		$this['billing_city'] = $billing_detail['billing_city'];
		$this['billing_state'] = $billing_detail['billing_state'];
		$this['billing_country'] = $billing_detail['billing_country'];
		$this['billing_pincode'] = $billing_detail['billing_pincode'];
		$this['billing_contact'] = $billing_detail['billing_contact'];
		
		$this['shipping_address'] = $billing_detail['shipping_address'];
		$this['shipping_city'] = $billing_detail['shipping_city'];
		$this['shipping_state'] = $billing_detail['shipping_state'];
		$this['shipping_pincode'] = $billing_detail['shipping_pincode'];
		$this['shipping_contact'] = $billing_detail['shipping_contact'];

		$this['currency_id'] = $this->app->epan->default_currency->id;
		$this['document_no'] = rand(111111,999999);
		$this['due_date'] = date('Y-m-d');
		$this['exchange_rate'] = $this->app->epan->default_currency['value'];

		//Todo Load Default TNC
		$tnc = $this->add('xepan\commerce\Model_TNC')->tryLoadAny();

		$this['tnc_id'] = $tnc['id'];
		$this['tnc_text'] = $tnc['content']?$tnc['content']:"not defined";

		$this->save();
						
		$cart_items=$this->add('xepan\commerce\Model_Cart');
		
		foreach ($cart_items as $junk) {
		
			$order_details = $this->add('xepan\commerce\Model_QSP_Detail');

			$item_model = $this->add('xepan\commerce\Model_Item')->load($cart_items['item_id']);

			$order_details['item_id'] = $item_model->id;
			$order_details['qsp_master_id']=$this->id;
			$order_details['quantity']=$cart_items['qty'];
			$order_details['price']=$cart_items['unit_price'];

			$order_details['extra_info'] = $cart_items['custom_fields'];//$item_model->customFieldsRedableToId(json_encode($cart_items['custom_fields']));
			
			$tax = $item_model->applyTax();
			$order_details['taxation_id'] = $tax['taxation_id'];
			$order_details['tax_percentage'] = $tax['tax_percent'];

			$order_details->save();

			// //todo many file_uplod_id
			// if($cart_items['file_upload_id']){
			// 	$atts = $this->add('xepan\commerce\Model_SalesOrderDetailAttachment');
			// 	$atts->addCondition('related_root_document_name','xShop\OrderDetail');
			// 	$atts->addCondition('related_document_id',$order_details->id);
			// 	$atts->tryLoadAny();
				
			// 	$atts['attachment_url_id'] = $cart_items['file_upload_id'];
			// 	$atts->save();
			// }
		}

		$this->createInvoice('Due');
		return $this;
	}

}
