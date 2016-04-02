<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','inprogress','manage_attachments'],
				'InProgress'=>['view','edit','delete','cancel','complete','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments'],
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
		return $this->items();
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


	function createInvoice($status='Approved',$order, $items_array=[],$amount=0,$discount=0,$shipping_charge=0,$narration,$shipping_address){
		$customer=$this->customer();
		// throw new \Exception($customer['currency_id'], 1);
		// throw new \Exception($this->app->epan->default_currency->get('id'), 1);
		
			$invoice = $this->add('xepan\commerce\Model_SalesInvoice');
			// $invoice['sales_order_id'] = $order->id;
			$invoice['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
			$invoice['related_qsp_master_id'] = $this->id;
			$invoice['contact_id'] = $customer->id;
			$invoice['epan_id'] = $this['epan_id'];
			$invoice['document_no'] =rand(1000,9999) ;
			$invoice['billing_address'] = $this['billing_address'];
			$invoice['billing_city'] = $this['billing_city'];
			$invoice['billing_state'] = $this['billing_state'];
			$invoice['billing_pincode'] = $this['billing_pincode'];
			$invoice['billing_contact'] = $this['billing_contact'];
			$invoice['billing_email'] = $this['billing_email'];
			$invoice['shipping_address'] = $shipping_address;

			$invoice['discount_amount'] = $discount?$discount:$this['discount_amount'];
			$invoice['tax'] = $this['tax'];
			$invoice['tnc_id'] = $this['tnc_id'];
			$invoice->save();

			$ois = $this->ref('Details');
			foreach ($ois as $oi) {	
			// 	if(count($items_array)) {
			// 		if(!in_array($oi['id'],$items_array)){
			// 			continue;
			// 		}
			// 	}
			// throw new \Exception($ois->count()->getOne()."I-".$oi->id.print_r($items_array,true));

				if($oi->invoice())
					throw new \Exception("Order Item already used in Invoice", 1);
					
				$invoice->addItem(
						$oi->item(),
						$oi['quantity'],
						$amount,
						$shipping_charge,
						null,
						null,
						$narration
						// $oi['extra_info'],
					);					
				// $invoice->updateAmounts();
				$oi->invoice($invoice);	
			}

			// if($status !== 'draft' and $status !== 'submitted'){				
			// 	// $invoice->createVoucher($salesLedger);
			// }

			return $invoice;
	}
}
