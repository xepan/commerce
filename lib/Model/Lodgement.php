<?php

//InvoiceTransactionAssociation
 namespace xepan\commerce;

 class Model_Lodgement extends \xepan\base\Model_Table{
 	public $table="lodgement";
 	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_QSP_Master','invoice_id');
		$this->hasOne('xepan\accounts\Model_Transaction','account_transaction_id');

		$this->addField('amount')->type('money')->defaultValue(0);
		$this->addField('currency');
		$this->addField('exchange_rate')->type('money');

		$this->addExpression('exchange_amount')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('amount'), $m->getElement('exchange_rate')]);
		})->type('money');

		$this->addHook('beforeDelete',$this);
	}

	// Do Lodgement of multiple invoice based on one transaction.
	function doLodgement($invoices=[],$transaction_id,$total_amount,$currency_id,$exchange_rate,$invoice_type="SalesInvoice"){
		$output = [];
		
		$total_amount_to_be_lodged = $total_amount;
		// echo "Total Amount to be lodged = ".$total_amount_to_be_lodged;
		//transaction amount must be greater then 0
		if(!$total_amount_to_be_lodged)
			return $output;		
		
		foreach ($invoices as $invoice_id) {
			if(!$total_amount_to_be_lodged)
				continue;

			if($invoice_type == "SalesInvoice")
				$selected_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
			else
				$selected_invoice = $this->add('xepan\commerce\Model_PurchaseInvoice');

			$selected_invoice->addExpression('logged_amount')->set(function($m,$q){
				$lodge_model = $m->add('xepan\commerce\Model_Lodgement')->addCondition('invoice_id',$q->getField('id'));
				return $lodge_model->sum($q->expr('IFNULL([0],0)',[$lodge_model->getElement('amount')]));
			})->type('money');

			$selected_invoice->addExpression('lodgement_amount')->set(function($m,$q){
				return $q->expr("([0]-IF([1],[1],0))",[$m->getElement('net_amount'),$m->getElement('logged_amount')]);
			})->type('money');
			$selected_invoice->load($invoice_id);

			if($total_amount_to_be_lodged > $selected_invoice['lodgement_amount']){
				$invoice_lodgement_amount = $selected_invoice['lodgement_amount'];
				$total_amount_to_be_lodged = $total_amount_to_be_lodged - $selected_invoice['lodgement_amount'];
			}else{
				$invoice_lodgement_amount = $total_amount_to_be_lodged;
				$total_amount_to_be_lodged = 0;
			}

			// echo "invoive loggeding amount=".$invoice_lodgement_amount."<br>";

			//save record into lodgement
			$lodgement_model = $this->add('xepan\commerce\Model_Lodgement');
			$lodgement_model['account_transaction_id'] = $transaction_id;
			$lodgement_model['invoice_id'] = $invoice_id;
			$lodgement_model['amount'] = $invoice_lodgement_amount;
			$lodgement_model['currency'] = $currency_id;
			$lodgement_model['exchange_rate'] = $exchange_rate;
			$lodgement_model->save();

			//create transaction for profit or loss
			
			if($invoice_type == "SalesInvoice")
				$customer_ledger = $this->add('xepan\commerce\Model_Customer')->load($selected_invoice['contact_id'])->ledger();
			else
				$customer_ledger = $this->add('xepan\commerce\Model_Supplier')->load($selected_invoice['contact_id'])->ledger();

			
			$transaction_exchange_amount = $invoice_lodgement_amount * $exchange_rate;
			$invoice_exchange_amount = $invoice_lodgement_amount * $selected_invoice['exchange_rate'];

			// echo "transaction_exchange_amount = ".$transaction_exchange_amount." = "."invoice_exchange_amount =".$invoice_exchange_amount."<br/>";
			// loss
			$abs_amount = 0;
			$gain_loss_amount = 0;
			if($transaction_exchange_amount < $invoice_exchange_amount){
				$gain_loss_transaction = $this->add('xepan\accounts\Model_Transaction');
				$gain_loss_transaction->createNewTransaction("EXCHANGE GAIN LOSS/PROFIT", $selected_invoice, $transaction_date=null, $Narration="Lodgement Id=".$lodgement_model->id, $currency_id, $exchange_rate,$related_id=$selected_invoice->id,$related_type="xepan\commerce_Model_".$invoice_type);

				$gain_loss_amount = $transaction_exchange_amount - $invoice_exchange_amount;
				$abs_amount = abs($gain_loss_amount);
				
				// echo "<br/> Loss amount = ".$abs_amount." transaction exchange amount= ".$transaction_exchange_amount." Invoive exchange amount= ".$invoice_exchange_amount;
				if($invoice_type == "SalesInvoice"){
					$exchange_loss_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Exchange Rate Different Loss");
					$gain_loss_transaction->addDebitLedger($exchange_loss_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->addCreditLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->execute();
				}else{
					$exchange_gain_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Exchange Rate Different Gain");
					$gain_loss_transaction->addCreditLedger($exchange_gain_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->addDebitLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->execute();
				}
			}elseif($transaction_exchange_amount > $invoice_exchange_amount){
				$gain_loss_transaction = $this->add('xepan\accounts\Model_Transaction');
				$gain_loss_transaction->createNewTransaction("EXCHANGE GAIN LOSS/PROFIT", $selected_invoice, $transaction_date=null, $Narration="Lodgement Id=".$lodgement_model->id, $currency_id, $exchange_rate,$related_id=$selected_invoice->id,$related_type="xepan\commerce_Model_".$invoice_type);

				//gain
				$gain_loss_amount = $invoice_exchange_amount - $transaction_exchange_amount;
				$abs_amount = abs($gain_loss_amount);
				
				// echo "<br/> gain amount = ".$abs_amount." transaction exchange amount= ".$transaction_exchange_amount." Invoive exchange amount= ".$invoice_exchange_amount;
				if($invoice_type == "SalesInvoice"){
					$exchange_gain_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Exchange Rate Different Gain");
					$gain_loss_transaction->addCreditLedger($exchange_gain_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->addDebitLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->execute();
				}else{
					$exchange_loss_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Exchange Rate Different Loss");
					$gain_loss_transaction->addDebitLedger($exchange_loss_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->addCreditLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
					$gain_loss_transaction->execute();
				}
			}

			

			//mark invoice paid
			$status =  'failed';
			// echo "net amount=".$selected_invoice['net_amount']." lodgged amount ".($invoice_lodgement_amount + $selected_invoice['logged_amount']);
			if($selected_invoice['net_amount'] == $invoice_lodgement_amount + $selected_invoice['logged_amount']){
				$selected_invoice->paid();
				$status = "success";
			}

			$output[$selected_invoice->id] = ['status'=>$status,'lodgement_amount'=>$invoice_lodgement_amount,'lodgement'=>$lodgement_model->id];
		}
		// throw new \Exception("Error Processing Request", 1);
		return $output;
	}

	function beforeDelete(){
		$qsp_m = $this->add('xepan\commerce\Model_QSP_Master');
		$qsp_m->tryLoad($this['invoice_id']?:0);

		if($qsp_m['type'] === "PurchaseInvoice"){
			$l_inv_m = $this->add('xepan\commerce\Model_PurchaseInvoice')->load($qsp_m->id);
		}elseif($qsp_m['type'] === "SalesInvoice") {
			$l_inv_m = $this->add('xepan\commerce\Model_SalesInvoice')->load($qsp_m->id);
		}

		if($l_inv_m->loaded()){
			$l_inv_m['status']="Due";
			$l_inv_m->save();
		}
	}

	function transactionRemoved($app,$transaction){
		$lodg_m = $this->add('xepan\commerce\Model_Lodgement');
		$lodg_m->addCondition('account_transaction_id',$transaction->id);
		foreach ($lodg_m as  $lodg) {
			$lodg->delete();
		}
	}
}
 
    