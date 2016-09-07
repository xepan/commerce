<?php

//InvoiceTransactionAssociation
 namespace xepan\commerce;

 class Model_Lodgement extends \xepan\base\Model_Table{
 	public $table="lodgement";
 	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_SalesInvoice','salesinvoice_id');
		$this->hasOne('xepan\accounts\Model_Transaction','account_transaction_id');

		$this->addField('amount')->type('money')->defaultValue(0);
		$this->addField('currency');
		$this->addField('exchange_rate')->type('money');

		$this->addExpression('exchange_amount')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('amount'), $m->getElement('exchange_rate')]);
		})->type('money');
	}

	// Do Lodgement of multiple invoice based on one transaction.
	function doLodgement($invoices=[],$transaction_id,$total_amount,$currency_id,$exchange_rate){
		$output = [];
		
		$total_amount_to_lodged = $total_amount;
		//transaction amount must be greater then 0
		if(!$total_amount_to_lodged)
			return $output;

		throw new \Exception($currency_id);
		
		
		foreach ($invoices as $invoice_id) {
			if(!$total_amount_to_lodged)
				continue;

			$selected_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
			$selected_invoice->addExpression('logged_amount')->set(function($m,$q){
				$lodge_model = $m->add('xepan\commerce\Model_Lodgement')->addCondition('salesinvoice_id',$q->getField('id'));
				return $lodge_model->sum($q->expr('IFNULL([0],0)',[$lodge_model->getElement('amount')]));
			})->type('money');

			$selected_invoice->addExpression('lodgement_amount')->set(function($m,$q){
				return $q->expr("([0]-IF([1],[1],0))",[$m->getElement('net_amount'),$m->getElement('logged_amount')]);
			})->type('money');
			$selected_invoice->load($invoice_id);
			
			if($total_amount_to_lodged > $selected_invoice['lodgement_amount']){
				$invoice_lodgement_amount = $selected_invoice['lodgement_amount'];
				$total_amount_to_lodged = $total_amount_to_lodged - $selected_invoice['lodgement_amount'];
			}else{
				$invoice_lodgement_amount = $total_amount_to_lodged;
				$total_amount_to_lodged = 0;
			}

			// throw new \Exception($total_amount_to_lodged." == ".$invoice_lodgement_amount);
			// echo "Total Amount to loged=".$invoice_lodgement_amount."<br>";

			//save record into lodgement
			$lodgement_model = $this->add('xepan\commerce\Model_Lodgement');
			$lodgement_model['account_transaction_id'] = $transaction_id;
			$lodgement_model['salesinvoice_id'] = $invoice_id;
			$lodgement_model['amount'] = $invoice_lodgement_amount;
			$lodgement_model['currency'] = $currency_id;
			$lodgement_model['exchange_rate'] = $exchange_rate;
			$lodgement_model->save();
			
			//create transaction for profit or loss
			$gain_loss_transaction = $this->add('xepan\accounts\Model_Transaction');
			$gain_loss_transaction->createNewTransaction("EXCHANGE GAIN LOSS/PROFIT", $selected_invoice, $transaction_date=null, $Narration="Lodgement Id=".$lodgement_model->id, $currency_id, $exchange_rate,$related_id=$selected_invoice->id,$related_type="xepan\commerce_Model_SalesInvoice");

			$customer_ledger = $this->add('xepan\commerce\Model_Customer')->load($selected_invoice['contact_id'])->ledger();
			
			$transaction_exchange_amount = $invoice_lodgement_amount * $exchange_rate;
			$invoice_exchange_amount = $invoice_lodgement_amount * $exchange_rate;

			// loss
			$abs_amount = 0;
			$gain_loss_amount = 0;
			if($transaction_exchange_amount > $invoice_exchange_amount){
				$gain_loss_amount = $transaction_exchange_amount - $invoice_exchange_amount;
				$abs_amount = abs($gain_loss_amount);
				
				$exchange_loss_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Exchange Rate Different Loss");
				$gain_loss_transaction->addCreditLedger($exchange_loss_ledger,$abs_amount,$this->app->epan->default_currency,1);
				$gain_loss_transaction->addDebitLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);

			}else{
				//gain
				$gain_loss_amount = $invoice_exchange_amount - $transaction_exchange_amount;
				$abs_amount = abs($gain_loss_amount);
				
				$exchange_gain_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Exchange Rate Different Gain");
				$gain_loss_transaction->addDebitLedger($exchange_gain_ledger,$abs_amount,$this->app->epan->default_currency,1);
				$gain_loss_transaction->addCreditLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
			}

			$gain_loss_transaction->execute();

			//mark invoice paid
			$status =  'failed';
			// echo "net amount=".$selected_invoice['net_amount']." lodgged amount ".($invoice_lodgement_amount + $selected_invoice['logged_amount']);
			if($selected_invoice['net_amount'] == $invoice_lodgement_amount + $selected_invoice['logged_amount']){
				$selected_invoice->paid();
				$status = "success";
			}

			$output[$selected_invoice->id] = ['status'=>$status,'lodgement_amount'=>$invoice_lodgement_amount,'lodgement'=>$lodgement_model->id];

		}

		// echo "<pre>";
		// print_r($output);
		return $output;
	}
}
 
    