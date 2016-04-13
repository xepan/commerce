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

	function convert(){
		$this['status']='Converted';
        $this->app->employee
            ->addActivity("Converted QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('send','Approved');
        $this->saveAndUnload();
    }

   


 //    function page_approve($page){

	// 	$page->add('View_Info')->setElement('H2')->setHTML('Approving Job Card will move this order to approved status and create JobCards to receive in respective FIRST Departments for EACH Item');

	// 	$form = $page->add('Form_Stacked');
	// 	$form->addField('text','comments');
	// 	$form->addSubmit('Approve & Create Jobcards');

	// 	if($form->isSubmitted()){
	// 		$this->approve();
			
	// 		$this['status']='InProgress';
 //        	$this->app->employee
 //            	->addActivity("SaleOrder Jobcard created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
 //            	->notifyWhoCan('','InProgress');
 //            $this->saveAndUnload();
 //            return true;
	// 	}
	// 	return false;
	// }

	// function approve(){
	// 	$this->app->hook('sales_order_approved',[$this]);
	// }

	// function orderItems(){
	// 	if(!$this->loaded())
	// 		throw new \Exception("loaded sale order required");

	// 	return $order_details = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
	// }

	// function customer(){
	// 	return $this->ref('contact_id');
	// }

	// function invoice(){
	// 	if(!$this->loaded());
	// 		throw new \Exception("Model Must Loaded, SaleOrder");
			
	// 	$inv = $this->add('xepan\commerce\Model_SalesInvoice')
	// 				->addCondition('related_qsp_master_id',$this->id);

	// 	$inv->tryLoadAny();
	// 	if($inv->loaded()) return $inv;
	// 	return false;
	// }


	// function createInvoice($status='Due',$items_array=[],$amount=0,$discount=0,$shipping_charge=0,$narration=null){
	// 	if(!$this->loaded())
	// 		throw new \Exception("model must loaded before creating invoice", 1);
		
	// 	$customer=$this->customer();
		
	// 	$invoice = $this->add('xepan\commerce\Model_SalesInvoice');

	// 	// $invoice['sales_order_id'] = $order->id;
	// 	$invoice['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
	// 	$invoice['related_qsp_master_id'] = $this->id;
	// 	$invoice['contact_id'] = $customer->id;
	// 	$invoice['status'] = $status;
	// 	$invoice['due_date'] = date('Y-m-d');
	// 	$invoice['exchange_rate'] = $this['exchange_rate'];
	// 	$invoice['document_no'] =rand(1000,9999) ;


	// 	$invoice['billing_address'] = $this['billing_address'];
	// 	$invoice['billing_city'] = $this['billing_city'];
	// 	$invoice['billing_state'] = $this['billing_state'];
	// 	$invoice['billing_country'] = $this['billing_country'];
	// 	$invoice['billing_pincode'] = $this['billing_pincode'];
	// 	$invoice['billing_contact'] = $this['billing_contact'];
	// 	$invoice['billing_email'] = $this['billing_email'];
		
	// 	$invoice['shipping_address'] = $this['shipping_address'];
	// 	$invoice['shipping_city'] = $this['shipping_city'];
	// 	$invoice['shipping_state'] = $this['shipping_state'];
	// 	$invoice['shipping_country'] = $this['shipping_country'];
	// 	$invoice['shipping_pincode'] = $this['shipping_pincode'];
	// 	$invoice['shipping_contact'] = $this['shipping_contact'];
	// 	$invoice['shipping_email'] = $this['shipping_email'];

	// 	$invoice['discount_amount'] = $this['discount_amount']?:0;
	// 	$invoice['tax'] = $this['tax'];
	// 	$invoice['tnc_id'] = $this['tnc_id'];
	// 	$invoice['tnc_text'] = $this['tnc_text']?$this['tnc_text']:"not defined";
	// 	$invoice->save();
			
	// 		//here this is current order
	// 		$ois = $this->orderItems();
	// 		foreach ($ois as $oi) {	
	// 			//todo check all invoice created or not
	// 			// $item,$qty,$price,$shipping_charge,$narration=null,$extra_info=null
	// 			$invoice->addItem(
	// 					$oi->item(),
	// 					$oi['quantity'],
	// 					$oi['price'],
	// 					$oi['shipping_charge'],
	// 					$oi['narration'],
	// 					$oi['extra_info']
	// 				);
	// 		}

	// 		// if($status !== 'draft' and $status !== 'submitted'){
	// 		// 	$invoice->createVoucher($salesLedger);
	// 		// }
	// 	return $invoice;
	// }

}
