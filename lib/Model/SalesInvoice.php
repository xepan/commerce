<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
	'Draft'=>['view','edit','delete','submit','manage_attachments'],
	'Submitted'=>['view','edit','delete','redesign','approve','manage_attachments','print_document'],
	'Redesign'=>['view','edit','delete','submit','manage_attachments'],
	'Due'=>['view','edit','delete','redesign','paid','send','cancel','manage_attachments','print_document'],
	'Paid'=>['view','edit','delete','send','cancel','manage_attachments','print_document'],
	'Canceled'=>['view','edit','delete','manage_attachments']
	];

	function init(){
		parent::init();

		$this->addCondition('type','SalesInvoice');
		$this->getElement('document_no')->defaultValue($this->newNumber());
		
		$nominal_field = $this->getField('nominal_id');
		$nominal_field->mandatory(true);

		$sale_group = $this->add('xepan\accounts\Model_Group')->load("Sales");
		$model = $nominal_field->getModel();
		
		$model->addCondition(
			$model->dsql()->orExpr()
			->where('root_group_id',$sale_group->id)
			->where('parent_group_id',$sale_group->id)
			->where('id',$sale_group->id)
			);

		$this->addHook('beforeDelete',[$this,'notifyDeletion']);
		$this->addHook('beforeDelete',[$this,'deleteTransactions']);

	}

	function print_document(){
		$this->print_QSP();
	}

	function page_send($page){
		$this->send_QSP($page,$this);
	}

	function redesign(){
		$this['status']='Redesign';
		$this->app->employee
		->addActivity("Sales Invoice no. '".$this['document_no']."' proceed for redesign", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('submit','Redesign',$this);
		$this->save();
	}


	function approve(){
		$this['status']='Due';		
		$this->app->employee
		->addActivity("Sales Invoice no. '".$this['document_no']."' due for rs. '".$this['net_amount']."' ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('redesign,paid,send,cancel','Due',$this);
		$this->updateTransaction();
		$this->save();		
	}

	function cancel(){
		$this['status']='Canceled';
        $this->app->employee
            ->addActivity("Sales Invoice no. '".$this['document_no']."' canceled ", $this->id /*Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
            ->notifyWhoCan('delete','Canceled');
		$this->deleteTransactions();
		$this->save();
	}

	function submit(){
		$this['status']='Submitted';
		$this->app->employee
		->addActivity("Sales Invoice no. '".$this['document_no']."' has submitted", $this->id, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('approve,reject','Submitted');
		$this->deleteTransactions();
		$this->save();
	}

	function page_paid($page){

		$ledger = $this->customer()->ledger();

		$pre_filled =[
			1 => [
				'party' => ['ledger'=>$ledger,'amount'=>$this['net_amount'],'currency'=>$this->ref('currency_id')]
			]
		];

		$et = $this->add('xepan\accounts\Model_EntryTemplate');
		$et->loadBy('unique_trnasaction_template_code','PARTYCASHRECEIVED');
		
		$et->addHook('afterExecute',function($et,$transaction,$total_amount,$row_data){
			$lodgement = $this->add('xepan\commerce\Model_Lodgement');
			
			$output = $lodgement->doLodgement(
					[$this->id],
					$transaction[0]->id,
					$total_amount,
					$row_data[0]['rows']['cash']['currency']?:$this->app->epan->default_currency,
					$row_data[0]['rows']['cash']['exchange_rate']
				);
			$this->app->page_action_result = $et->form->js()->univ()->closeDialog();
		});

		$v = $page->add('View',null,null,['view/accountsform/amtrecevied']);
		$view_cash = $v->add('View',null,'cash_view');
		$et->manageForm($view_cash,$this->id,'xepan\commerce\Model_SalesInvoice',$pre_filled);

		$et_bank = $this->add('xepan\accounts\Model_EntryTemplate');
		$et_bank->loadBy('unique_trnasaction_template_code','PARTYBANKRECEIVED');
		
		$et_bank->addHook('afterExecute',function($et_bank,$transaction,$total_amount){
			// Do Lodgement
			$this->app->page_action_result = $et_bank->form->js()->univ()->closeDialog();
		});
		$view_bank = $v->add('View',null,'bank_view');
		$et_bank->manageForm($view_bank,$this->id,'xepan\commerce\Model_SalesInvoice',$pre_filled);
	}

	function paid(){
		$this['status']='Paid';
		$this->app->employee
		->addActivity(" Amount '".$this['net_amount']."' of sales invoice no. '".$this['document_no']."' have been recieved  ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('send,cancel','Paid');
		$this->save();
	}


	function PayViaOnline($transaction_reference,$transaction_reference_data){
		$this['transaction_reference'] =  $transaction_reference;
		$this['transaction_response_data'] = json_encode($transaction_reference_data);
		$this->save();
		$this->paid();
		$this->app->hook('invoice_paid',[$this]);
	}

	function notifyDeletion(){
		$this->app->employee
		->addActivity("Invoice Deleted", $this->id, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('approve,reject','Submitted');
	}

	function deleteTransactions(){
		$old_transaction = $this->add('xepan\accounts\Model_Transaction');
		$old_transaction->addCondition('related_id',$this->id);
		$old_transaction->addCondition('related_type',"xepan\commerce\Model_SalesInvoice");

		if($old_transaction->count()->getOne()){
			$old_transaction->tryLoadAny();
			$old_transaction->deleteTransactionRow();
			$old_transaction->delete();
		}
	}

	function updateTransaction($delete_old=true,$create_new=true){		
		if(!$this->loaded())
			throw new \Exception("model must loaded for updating transaction");
		
		if(!in_array($this['status'], ['Due','Paid']))			
			return;

		if($delete_old){			
		//saleinvoice model transaction have always one entry in transaction
			$this->deleteTransactions();
		}


		if($create_new){
			$new_transaction = $this->add('xepan\accounts\Model_Transaction');
			$new_transaction->createNewTransaction("SalesInvoice",$this,$this['created_at'],'Sale Invoice',$this->currency(),$this['exchange_rate'],$this['id'],'xepan\commerce\Model_SalesInvoice');
									
			//DR
			//Load Party Ledger
			$customer_ledger = $this->add('xepan\commerce\Model_Customer')->load($this['contact_id'])->ledger();
			
			$new_transaction->addDebitLedger($customer_ledger,$this['net_amount'],$this->currency(),$this['exchange_rate']);
			
			//Load Discount Ledger
			$discount_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Rebate & Discount");
			$new_transaction->addDebitLedger($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);
			
			//Load Round Ledger
			$round_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Round Account");
			if($this['round_amount'] < 0)
				$new_transaction->addCreditLedger($round_ledger,abs($this['round_amount']),$this->currency(),$this['exchange_rate']);
			else
				$new_transaction->addDebitLedger($round_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);

			//CR
			//Load Sale Ledger
			$sale_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Sales Account");
			$new_transaction->addCreditLedger($sale_ledger, $this['total_amount'], $this->currency(), $this['exchange_rate']);

			// //Load Multiple Tax Ledger according to sale invoice item
			$comman_tax_array = [];
			foreach ($this->details() as $invoice_item) {
				if( $invoice_item['taxation_id']){
					if(!in_array( trim($invoice_item['taxation_id']), array_keys($comman_tax_array)))
						$comman_tax_array[$invoice_item['taxation_id']]= 0;
					$comman_tax_array[$invoice_item['taxation_id']] += round($invoice_item['tax_amount'],2);
				}
			}

			foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
				$tax_model = $this->add('xepan\commerce\Model_Taxation')->load($tax_id);
				$tax_ledger = $tax_model->ledger();
				$new_transaction->addCreditLedger($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate'],$tax_model['sub_tax']);
			}

			
			$new_transaction->execute();
		}
	}

	function addItem($item,$qty,$price,$sale_amount,$original_amount,$shipping_charge,$shipping_duration,$express_shipping_charge=null,$express_shipping_duration=null,$narration=null,$extra_info=null,$taxation_id=null,$tax_percentage=null){
		if(!$this->loaded())
			throw new \Exception("SalesInvoice must loaded", 1);

		if(!$taxation_id and $tax_percentage){
			$tax = $item->applicableTaxation();
			$taxation_id = $tax['taxation_id'];
			$tax_percentage = $tax['tax_percentage'];
		}

		$in_item = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
		$in_item['item_id'] = $item->id;
		$in_item['qsp_master_id'] = $this->id;
		$in_item['quantity'] = $qty;
		$in_item['price'] = $price;
		$in_item['shipping_charge'] = $shipping_charge;
		$in_item['shipping_duration'] = $shipping_duration;
		$in_item['sale_amount'] = $sale_amount;
		$in_item['original_amount'] = $original_amount;
		$in_item['shipping_duration'] = $shipping_duration;
		$in_item['express_shipping_charge'] = $express_shipping_charge;
		$in_item['express_shipping_duration'] = $express_shipping_duration;
		$in_item['narration'] = $narration;
		$in_item['extra_info'] = $extra_info;
		$in_item['taxation_id'] = $taxation_id;
		$in_item['tax_percentage'] = $tax_percentage;

		$in_item->save();

	}

	function saleOrder(){
		if(!$this->loaded())
			throw new \Exception("sale invoice must loaded", 1);
		
		if(!$this['related_qsp_master_id'])
			return false;
		
		$saleorder = $this->add('xepan\commerce\Model_SalesOrder')->tryLoad($this['related_qsp_master_id']);

		if(!$saleorder->loaded())
			return false;

		return $saleorder;
	}


	function customer(){
		return $this->add('xepan\commerce\Model_Customer')->tryLoad($this['contact_id']);
	}



}
