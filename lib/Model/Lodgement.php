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

	function do_lodgement($lodge_array,$selected_transaction_id,$selected_trans){
		// echo "<pre>";
		// print_r($lodge_array);
		// exit;

		$selected_invoice = $this->add('xepan\commerce\Model_SalesInvoice')->load($lodge_array['invoice_id']);

		//save record into lodgement
		$lodgement_model = $this->add('xepan/commerce/Model_Lodgement');
		$lodgement_model['account_transaction_id'] = $selected_transaction_id;
		$lodgement_model['salesinvoice_id'] = $lodge_array['invoice_id'];
		$lodgement_model['amount'] = $lodge_array['invoice_adjust'];
		$lodgement_model['currency'] = $selected_trans['currency_id'];
		$lodgement_model['exchange_rate'] = $selected_trans['exchange_rate'];
		$lodgement_model->save();

		//create transaction for profit or loss
		
		$currency = $this->add('xepan\accounts\Model_Currency')->load($selected_trans['currency_id']);
		$transaction = $this->add('xepan\accounts\Model_Transaction');
		$transaction->createNewTransaction("EXCHANGE GAIN LOSS/PROFIT", $selected_invoice, $transaction_date=null, $Narration="Lodgement Id=".$lodgement_model->id, $currency, $selected_trans['exchange_rate'],$related_id=$lodge_array['invoice_id'],$related_type="xepan\commerce_Model_SalesInvoice");


		$customer_ledger = $this->add('xepan\commerce\Model_Customer')->load($selected_invoice['contact_id'])->ledger();
		$abs_amount = abs($lodge_array['invoice_gain_loss']);

		
		
		if($lodge_array['invoice_gain_loss'] < 0){
		//profit
			$exchange_gain_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultExchangeGain();

			$transaction->addDebitLedger($exchange_gain_ledger,$abs_amount,$this->app->epan->default_currency,1);
			$transaction->addCreditLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
		}

		if($lodge_array['invoice_gain_loss'] > 0){
		//Loss
			$exchange_loss_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultExchangeLoss();

			$transaction->addCreditLedger($exchange_loss_ledger,$abs_amount,$this->app->epan->default_currency,1);
			$transaction->addDebitLedger($customer_ledger,$abs_amount,$this->app->epan->default_currency,1);
		}

		$transaction->execute();

		//mark invoice paid
		if($selected_invoice['net_amount'] === $lodge_array['invoice_adjust'])
			$selected_invoice->paid();

	}
}
 
    