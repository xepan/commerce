<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\commerce\Model_QSP_Master{
	
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
	'Draft'=>['view','edit','delete','submit','manage_attachments'],
	'Submitted'=>['view','edit','delete','redesign','reject','approve','manage_attachments','print_document'],
	'Approved'=>['view','edit','delete','send','redesign','reject','convert','manage_attachments','createOrder','print_document'],
	'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
	'Rejected'=>['view','edit','delete','redesign','manage_attachments'],
	'Converted'=>['view','edit','delete','send','createOrder','manage_attachments','print_document']
	];

	function init(){
		parent::init();

		$this->addCondition('type','Quotation');
		$this->getElement('document_no')->defaultValue($this->newNumber());

		$this->is([
			'document_no|required|number|unique_in_epan_for_type'
			]);

	}

	function print_document(){
		$this->print_QSP();
	}

	function submit(){
		$this['status']='Submitted';
		$this->app->employee
            ->addActivity("Quotation No : '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_quotationdetail&document_id=".$this->id."")
            ->notifyWhoCan('redesign,reject,approve','Submitted',$this);
		$this->save();
	}

	function redesign(){
		$this['status']='Redesign';
		$this->app->employee
		->addActivity("Quotation No : '".$this['document_no']."' proceed for redesign", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_quotationdetail&document_id=".$this->id."")
		->notifyWhoCan('reject','Redesign',$this);
		$this->save();
	}

	function reject(){
		$this['status']='Rejected';
		$this->app->employee
		->addActivity("Quotation No : '".$this['document_no']."' rejected", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_quotationdetail&document_id=".$this->id."")
		->notifyWhoCan('redesign','Rejected',$this);
		$this->save();
	}

	function approve(){
		$this['status']='Approved';
		$this->app->employee
		->addActivity("Quotation No : '".$this['document_no']."' approved", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_quotationdetail&document_id=".$this->id."")
		->notifyWhoCan('redesign,reject,convert,send','Approved',$this);
		$this->save();
	}

	function page_send($page){
		$this->send_QSP($page,$this);
	}

	function convert(){
		$this['status']='Converted';
		$this->app->employee
		->addActivity("Quotation No :. '".$this['document_no']."' converted successfully to order", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_quotationdetail&document_id=".$this->id."")
		->notifyWhoCan('send','Converted');
		$this->save();
	}

	function quotationItems(){
		if(!$this->loaded())
			throw new \Exception("loaded quotation required");

		return $quotation_details = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
	}

	function customer(){
		return $this->ref('contact_id');
	}

	function order(){
		if(!$this->loaded())
			throw new \Exception("Model Must Loaded, Quotation");
		
		$ord = $this->add('xepan\commerce\Model_SalesOrder')
		->addCondition('related_qsp_master_id',$this->id);

		$ord->tryLoadAny();
		if($ord->loaded()) return $ord;
		return false;
	}


	function page_createOrder($page){
		$page->add('View')->set('Quotation No: '.$this['document_no']);
		
		if(!$this->loaded()){
			$page->add('View_Error')->set("model must loaded");
			return;
		}

		$ord = $this->order();
		if(!$ord){
			$page->add('View')->set("You have successfully created order of this quotation, you can edit too ");
			$new_order = $this->createOrder();
			$form = $page->add('Form');
			$form->addSubmit('Edit Order');
			if($form->isSubmitted()){
				return $form->js()->univ()->location($this->api->url('xepan_commerce_salesorderdetail',['action'=>'edit','document_id'=>$new_order->id]));
			}
			$page->add('xepan\commerce\View_QSP',['qsp_model'=>$new_order]);
		}else{

			$page->add('View')->set("You have created order of this quotation");
			$form = $page->add('Form');
			$form->addSubmit('Edit Order');
				if($form->isSubmitted()){
					return $form->js()->univ()->location($this->api->url('xepan_commerce_salesorderdetail',['action'=>'edit','document_id'=>$ord->id]));
				}
			$page->add('xepan\commerce\View_QSP',['qsp_model'=>$ord]);
		}
	}

	function createOrder(){
		if(!$this->loaded())
			throw new \Exception("model must loaded before creating order", 1);
		
		$customer=$this->customer();
		
		$tnc_model = $this->add('xepan\commerce\Model_TNC')->tryLoad($this['tnc_id']);
		
		$order = $this->add('xepan\commerce\Model_SalesOrder');

		$due_date = $this->app->now;
		// if($this['due_date'] > $this['created_at']){
		// 	$due_date = $this['due_date'];
		// }

		$order['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		$order['related_qsp_master_id'] = $this->id;
		$order['contact_id'] = $customer->id;
		$order['status'] = 'Draft';
		$order['due_date'] = $due_date;
		$order['exchange_rate'] = $this['exchange_rate'];
		$order['document_no'] = $order['document_no'];
		
		$order['billing_address'] = $this['billing_address'];
		$order['billing_city'] = $this['billing_city'];
		$order['billing_state_id'] = $this['billing_state_id'];
		$order['billing_country_id'] = $this['billing_country_id'];
		$order['billing_pincode'] = $this['billing_pincode'];
		
		$order['shipping_address'] = $this['shipping_address'];
		$order['shipping_city'] = $this['shipping_city'];
		$order['shipping_state_id'] = $this['shipping_state_id'];
		$order['shipping_country_id'] = $this['shipping_country_id'];
		$order['shipping_pincode'] = $this['shipping_pincode'];

		$order['discount_amount'] = $this['discount_amount']?:0;
		$order['tnc_id'] = $tnc_model?$tnc_model->id:$this['tnc_id'];
		$order['tnc_text'] = $tnc_model?$tnc_model['content']:$this['tnc_text'];
		
		$order->save();
		
			//here this is current quotation
		$ois = $this->quotationItems();
		foreach ($ois as $oi) {	
			$order->addOrdItem(
				$oi->item(),
				$oi['quantity'],
				$oi['price'],
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
		return $order;
	}
}
