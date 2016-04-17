<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\commerce\Model_QSP_Master{
	
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','approve','manage_attachments','createOrder'],
				'Approved'=>['view','edit','delete','redesign','reject','convert','manage_attachments','createOrder'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Rejected'=>['view','edit','delete','redesign','manage_attachments'],
				'Converted'=>['view','edit','delete','send','manage_attachments']
				];

	function init(){
		parent::init();

		$this->addCondition('type','Quotation');

	}


	function submit(){
		$this['status']='Submitted';
        $this->app->employee
            ->addActivity("Draft QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('submit','Draft');
        $this->saveAndUnload();
    }

    function redesign(){
		$this['status']='Redesign';
        $this->app->employee
            ->addActivity("Submitted QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('redesign,approve','Submitted');
        $this->saveAndUnload();
    }

    function reject(){
		$this['status']='Rejected';
        $this->app->employee
            ->addActivity("Submitted QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('redesign,approve','Submitted');
        $this->saveAndUnload();
    }

    function approve(){
		$this['status']='Approved';
        $this->app->employee
            ->addActivity("Submitted QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','Submitted');
        $this->saveAndUnload();
    }


	function convert(){
		$this['status']='Converted';
        $this->app->employee
            ->addActivity("Converted QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('send','Approved');
        $this->saveAndUnload();
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
		if(!$this->loaded());
			throw new \Exception("Model Must Loaded, Quotation");
			
		$ord = $this->add('xepan\commerce\Model_SalesOrder')
					->addCondition('related_qsp_master_id',$this->id);

		$ord->tryLoadAny();
		if($ord->loaded()) return $ord;
		return false;
	}

	function page_createOrder($page){
		$page->add('View')->set('Quotation No: '.$this['id']);
		if(!$this->loaded()){
			$page->add('View_Error')->set("model must loaded");
			return;
		}

		$form = $page->add('Form');
		$form->addSubmit('create Order');
		if($form->isSubmitted()){

			$order = $this->createOrder();

			return $form->js()->univ()->location($this->api->url('xepan_commerce_salesorderdetail',['action'=>'edit','document_id'=>$order->id]));
		}

	}

	function createOrder(){
		if(!$this->loaded())
			throw new \Exception("model must loaded before creating order", 1);
		
		$customer=$this->customer();
		
		$order = $this->add('xepan\commerce\Model_SalesOrder');

		$order['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		$order['related_qsp_master_id'] = $this->id;
		$order['contact_id'] = $customer->id;
		$order['status'] = 'Draft';
		$order['due_date'] = date('Y-m-d');
		$order['exchange_rate'] = $this['exchange_rate'];
		$order['document_no'] =rand(1000,9999) ;
	
		$order['billing_address'] = $this['billing_address'];
		$order['billing_city'] = $this['billing_city'];
		$order['billing_state'] = $this['billing_state'];
		$order['billing_country'] = $this['billing_country'];
		$order['billing_pincode'] = $this['billing_pincode'];
		$order['billing_contact'] = $this['billing_contact'];
		$order['billing_email'] = $this['billing_email'];
		
		$order['shipping_address'] = $this['shipping_address'];
		$order['shipping_city'] = $this['shipping_city'];
		$order['shipping_state'] = $this['shipping_state'];
		$order['shipping_country'] = $this['shipping_country'];
		$order['shipping_pincode'] = $this['shipping_pincode'];
		$order['shipping_contact'] = $this['shipping_contact'];
		$order['shipping_email'] = $this['shipping_email'];

		$order['discount_amount'] = $this['discount_amount']?:0;
		$order['tnc_id'] = $this['tnc_id'];
		$order['tnc_text'] = $this['tnc_text']?$this['tnc_text']:"not defined";
		
		$order->save();
				
			//here this is current quotation
			$ois = $this->quotationItems();
			foreach ($ois as $oi) {	
				$order->addOrdItem(
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
		return $order;
	}
}
