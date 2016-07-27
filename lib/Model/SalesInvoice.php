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

		$sale_group = $this->add('xepan\accounts\Model_Group')->loadRootSalesGroup();
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
		$this->saveAndUnload();
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
		$this->saveAndUnload();
	}

	function submit(){
		$this['status']='Submitted';
		$this->app->employee
		->addActivity("Sales Invoice no. '".$this['document_no']."' has submitted", $this->id, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('approve,reject','Submitted');
		$this->deleteTransactions();
		$this->saveAndUnload();
	}

	function page_paid($page){

		$v = $page->add('View',null,null,['view/accountsform/amtrecevied']);
		// CASH RECIEVING
		$received_from_model = $this->add('xepan\accounts\Model_Ledger');
		$received_from_model->addCondition('contact_id',$this['contact_id']);

		$cash_ledgers = $this->add('xepan\accounts\Model_Ledger')->filterCashLedgers();
		$cash_ledgers->filterCashLedgers();

		$form = $v->add('Form',null,'cash_view');
		$form->setLayout('view/accountsform/payment-received-cash');

		$form->addField('DatePicker','date')->set($this->api->now)->validate('required');
		$cash_field = $form->addField('autocomplete/Basic','cash_account')->validate('required');
		$cash_field->setModel($cash_ledgers);

		$cash_field->set($this->add('xepan\accounts\Model_Ledger')->loadDefaultCashLedger()->get('id'));
		
		$received_from_field = $form->addField('autocomplete/Basic','received_from')->validateNotNull(true);
		$received_from_field->setModel($received_from_model);
		// $received_from_field->set($received_from_model['name']);

		$form->addField('Money','amount')->validate('required');
		$form->addField('Text','narration');
		$form->addSubmit('Receive Now')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			
			$transaction = $this->add('xepan\accounts\Model_Transaction');
			$transaction->createNewTransaction('CASH RECEIPT', null, $form['date'], $form['narration']);
			
			$transaction->addDebitLedger($this->add('xepan\accounts\Model_Ledger')->load($form['cash_account']),$form['amount']);
			
			$transaction->addCreditLedger($this->add('xepan\accounts\Model_Ledger')->load($form['received_from']),$form['amount']);

			$transaction->execute();
			if($form['amount'] == $this['net_amount']){
				$this->paid();
			}else{
				
				$selected_transaction_id = $transaction->id;
				$selected_trans = $this->add('xepan\accounts\Model_Transaction')->load($selected_transaction_id);

				$transaction_amount_adjust = 0;
				$adjust = 0;
				if($form['amount'] > $transaction_amount_adjust){
					$transaction_amount_adjust += $this['net_amount'];
					if($form['amount'] > $transaction_amount_adjust ){
						$adjust = $this['net_amount'];
					}else if($form['amount'] - ($transaction_amount_adjust - $this['net_amount']) > 0){
						$adjust = $form['amount'] - ($transaction_amount_adjust - $this['net_amount']);
					}else
					$adjust = 0;
				}
				$field_adjust_amount = $adjust;

				$field_profit_loss = 0;

				$according_invoice_exchange_amount = $this['exchange_rate'] * $adjust;
				// $according_transaction_exchange_amount = $selected_trans['exchange_rate'] * $adjust;

				if($field_adjust_amount){
					$amount = $field_adjust_amount;
					$according_invoice_exchange_amount = $this['exchange_rate'] * $amount;
					// $according_transaction_exchange_amount = $selected_trans['exchange_rate'] * $amount;
					
				}
				
				$field_profit_loss =  $according_invoice_exchange_amount;

				$lodge_array = array('invoice_id' =>$this['id'] ,
										'invoice_no'=>$this['document_no'] ,
										'invoice_amount'=>$this['net_amount'],
										'invoice_currency'=>$this['currency_id'],
										'invoice_exchange_rate'=>$this['exchange_rate'],
										'invoice_adjust'=>$field_adjust_amount,
										'invoice_gain_loss'=>$field_profit_loss);

				$lodgement = $this->add('xepan\commerce\Model_Lodgement');
				$lodgement->do_lodgement($lodge_array,$selected_transaction_id,$selected_trans);
			}
			return $form->js(null,$form->js()->reload())->univ()->closeDialog()->successMessage('Done');
		}

		// BANK RECIEVING 
		$received_from_model = $this->add('xepan\accounts\Model_Ledger');
		$received_from_model->addCondition('contact_id',$this['contact_id']);


		$form = $v->add('Form_Stacked',null,'bank_view');
		$form->setLayout('view/form/payment-received-bank');
		
		/*Received From*/
		$received_from_field = $form->addField('autocomplete/Basic','received_from')->validate('required');
		$received_from_field->setModel($received_from_model);
		$received_from_field->set($received_from_model['name']);
		$form->addField('Money','from_amount')->validateNotNull(true);

		$from_curreny_field=$form->addField('Dropdown','from_currency')->validate('required');
		$from_curreny_field->setModel('xepan\accounts\Currency');
		$from_curreny_field->set($this->app->epan->default_currency->id);
		$form->addField('line','from_exchange_rate')->validate('required');

		$form->addField('DatePicker','date')->set($this->api->now)->validate('required');

		$bank_ledgers = $this->add('xepan\accounts\Model_Ledger')->filterBankLedgers();
		// /*To Details*/
		$bank_field = $form->addField('autocomplete/Basic','to_bank_account')->validateNotNull(true);
		$bank_field->setModel($bank_ledgers);
		$bank_field->set($this->add('xepan\accounts\Model_Ledger')->loadDefaultBankLedger()->get('id'));

		$to_curreny_field = $form->addField('Dropdown','to_currency')->validateNotNull(true);
		$to_curreny_field->setModel('xepan\accounts\Currency');
		$to_curreny_field->set($this->app->epan->default_currency->id);
		$form->addField('Money','to_amount')->validateNotNull(true);
		$form->addField('line','to_exchange_rate')->validateNotNull(true);
		
		// /*Different Charges*/
		for ($i=1; $i < 6; $i++) {
			$bank_field_1 = $form->addField('autocomplete/Basic','bank_account_charges_'.$i);//->validateNotNull(true);
			$bank_field_1->setModel('xepan\accounts\Model_Ledger')->filterBankCharges();
			$bank_field_1_charge_amount = $form->addField('Money','bank_charge_amount_'.$i);//->validateNotNull(true);
			$bank_field_1_currency = $form->addField('Dropdown','bank_currency_'.$i);//->validateNotNull(true);
			$bank_field_1_currency->setModel('xepan\accounts\Currency');
			$bank_field_1_exchange_rate = $form->addField('line','bank_exchange_rate_'.$i);
			$bank_field_1_is_external_charge = $form->addField('checkbox','is_external_charge_'.$i);

		}

		$form->addField('Text','narration');
		$form->addSubmit('Receive Now')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			
				//Customer account
			$from_ledger = $this->add('xepan\accounts\Model_Ledger')->load($form['received_from']);
			$from_currency = $this->add('xepan\accounts\Model_Currency')->load($form['from_currency']);

			$transaction1 = $this->add('xepan\accounts\Model_Transaction');
			$transaction1->createNewTransaction('BANK RECEIPT', $related_document=false, $form['date'], $form['narration'], $from_currency, $form['from_exchange_rate'],$related_id=$form['received_from'],$related_type="xepan\accounts\Model_Ledger");

			$transaction1->addCreditLedger($from_ledger,$form['from_amount'],$from_currency,$form['from_exchange_rate']);
				// echo "DR From Account: ".$from_ledger->id." :amount= ".$form['from_amount']." :Currency= ".$from_currency->id." :exchange Rate=".$form['from_exchange_rate']."<br/>";

			//one entry for to bank 
			$to_bank_ledger = $this->add('xepan\accounts\Model_Ledger')->load($form['to_bank_account']);
			$to_bank_currency = $this->add('xepan\accounts\Model_Currency')->load($form['to_currency']);

			
			$transaction2 = $this->add('xepan\accounts\Model_Transaction');
			$transaction2->createNewTransaction('BANK  CHARGES RECEIPT', $related_document=false, $form['date'], $form['narration'], $from_currency, $form['from_exchange_rate'],$related_type="xepan\accounts\Model_Ledger");

			$charges = 0;

			for ($i=1; $i < 6; $i++) {
				
				$bank_field = "bank_account_charges_".$i;
				$amount_field = "bank_charge_amount_".$i;
				$currency_field = "bank_currency_".$i;
				$exchange_field = "bank_exchange_rate_".$i;
				$external_charge_field = "is_external_charge_".$i;

				if(!$form[$bank_field])
					continue;
				//TODO :: check for date, charge_amount, Currency, Exchange_rate

				if(!$form[$external_charge_field]){
					$charges +=  $form[$amount_field];
				}
				
				$bank_other_charge_ledger = $this->add('xepan\accounts\Model_Ledger')->load($form[$bank_field]);
				$bank_other_charge_currency = $this->add('xepan\accounts\Model_Currency')->load($form[$currency_field]);

				if($charges){
					if($form[$external_charge_field]){
						$transaction1->addDebitLedger($bank_other_charge_ledger,$form[$amount_field],$bank_other_charge_currency,$form[$exchange_field]);
					}
					if(!$form[$external_charge_field]){
						$transaction2->addDebitLedger($bank_other_charge_ledger,$form[$amount_field],$bank_other_charge_currency,$form[$exchange_field]);
					}
				}
			}
			
			// $transaction1->addDebitLedger($bank_other_charge_ledger,$form[$amount_field]);
			if(!$charges){
				$transaction1->addDebitLedger($to_bank_ledger,$form['to_amount'],$to_bank_currency,$form['to_exchange_rate']);
				$amount_lodg = $form['to_amount'];
			}else{
				$transaction1->addDebitLedger($to_bank_ledger,$form['to_amount'] + $charges);
				$amount_lodg = $form['to_amount'] + $charges;
				$transaction2->addCreditLedger($to_bank_ledger,$charges);
				$transaction2->execute();
			}
				$transaction1->execute();
				if($amount_lodg == $this['net_amount']){
					$this->paid();
				}else{

					$selected_transaction_id = $transaction1->id;
					$selected_trans = $this->add('xepan\accounts\Model_Transaction')->load($selected_transaction_id);

					$transaction_amount_adjust = 0;
					$adjust = 0;
					if($amount_lodg > $transaction_amount_adjust){
						$transaction_amount_adjust += $this['net_amount'];
						if($amount_lodg > $transaction_amount_adjust ){
							$adjust = $this['net_amount'];
						}else if($amount_lodg - ($transaction_amount_adjust - $this['net_amount']) > 0){
							$adjust = $amount_lodg - ($transaction_amount_adjust - $this['net_amount']);
						}else
						$adjust = 0;
					}
					$field_adjust_amount = $adjust;

					$field_profit_loss = 0;

					$according_invoice_exchange_amount = $this['exchange_rate'] * $adjust;
					$according_transaction_exchange_amount = $selected_trans['exchange_rate'] * $adjust;
					
					if($field_adjust_amount){
						$amount = $field_adjust_amount;
						$according_invoice_exchange_amount = $this['exchange_rate'] * $amount;
						$according_transaction_exchange_amount = $selected_trans['exchange_rate'] * $amount;

					}
					
					$field_profit_loss = ($according_transaction_exchange_amount - $according_invoice_exchange_amount);

					$lodge_array = array('invoice_id' =>$this['id'] ,
											'invoice_no'=>$this['document_no'] ,
											'invoice_amount'=>$this['net_amount'],
											'invoice_currency'=>$this['currency_id'],
											'invoice_exchange_rate'=>$this['exchange_rate'],
											'invoice_adjust'=>$field_adjust_amount,
											'invoice_gain_loss'=>$field_profit_loss);

					$lodgement = $this->add('xepan\commerce\Model_Lodgement');
					$lodgement->do_lodgement($lodge_array,$selected_transaction_id,$selected_trans);

				}

			return $form->js(null,$form->js()->reload())->univ()->successMessage('Done');
		}
	}

	function paid(){
		$this['status']='Paid';
		$this->app->employee
		->addActivity(" Amount '".$this['net_amount']."' of sales invoice no. '".$this['document_no']."' have been recieved  ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('send,cancel','Paid');
		$this->saveAndUnload();
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
			$discount_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultDiscountGivenLedger();
			$new_transaction->addDebitLedger($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);
			
			//Load Round Ledger
			$round_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultRoundLedger();
			$new_transaction->addDebitLedger($discount_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);

			//CR
			//Load Sale Ledger
			$sale_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultSalesLedger();
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
				$new_transaction->addCreditLedger($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate']);
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
			throw new \Exception("Related order not found", 1);
		
		$saleorder = $this->add('xepan\commerce\Model_SalesOrder')->tryLoad($this['related_qsp_master_id']);

		if(!$saleorder->loaded())
			throw new \Exception("Related order not found", 1);			

		return $saleorder;
	}



}
