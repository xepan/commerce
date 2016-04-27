<?php

namespace xepan\commerce;

class Model_QSP_Master extends \xepan\hr\Model_Document{

	function init(){
		parent::init();

		$qsp_master_j = $this->join('qsp_master.document_id');
		$qsp_master_j->hasOne('xepan/base/Contact','contact_id')->sortable(true);
		$qsp_master_j->hasOne('xepan/accounts/Currency','currency_id');
		$qsp_master_j->hasOne('xepan/accounts/Group','nominal_id');
		$qsp_master_j->hasOne('xepan/commerce/TNC','tnc_id');
		$qsp_master_j->hasOne('xepan/commerce/PaymentGateway','paymentgateway_id');

		//Related QSP Master
		$qsp_master_j->hasOne('xepan\commerce\RelatedQspMaster','related_qsp_master_id')->defaultValue('Null');
		
		$qsp_master_j->addField('document_no')->sortable(true);
		$this->addExpression('document_no_number')->set('CAST(document_no AS decimal)')->sortable(true);

		// $qsp_master_j->addField('billing_landmark');
		$qsp_master_j->addField('billing_address');
		$qsp_master_j->addField('billing_city');
		$qsp_master_j->addField('billing_state');
		$qsp_master_j->addField('billing_country');
		$qsp_master_j->addField('billing_pincode');
		$qsp_master_j->addField('billing_contact');
		$qsp_master_j->addField('billing_email');

		// $qsp_master_j->addField('shipping_landmark');
		$qsp_master_j->addField('shipping_address');
		$qsp_master_j->addField('shipping_city');
		$qsp_master_j->addField('shipping_state');
		$qsp_master_j->addField('shipping_country');
		$qsp_master_j->addField('shipping_pincode');
		$qsp_master_j->addField('shipping_contact');
		$qsp_master_j->addField('shipping_email');
		
		//Total Amount: calculate sum al  item field amount_excluding_tax
		$this->addExpression('total_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $details->sum('amount_excluding_tax');
		})->type('money');

		// $qsp_master_j->addField('gross_amount'); 
		//Total Item amount Sum
		$this->addExpression('gross_amount')->set(function($m,$q){
			$details = $m->refSQL('Details');
			return $details->sum('total_amount');
		})->type('money');
		

		$qsp_master_j->addField('discount_amount')->defaultValue(0); 

		$this->addExpression('net_amount')->set(function($m,$q){
			return $q->expr('([0] - [1])',[$m->getElement('gross_amount'), $m->getElement('discount_amount')]);
		})->type('money');

				

		$qsp_master_j->addField('due_date')->type('datetime');
		$qsp_master_j->addField('priority_id');
		$qsp_master_j->addField('narration')->type('text');

		$qsp_master_j->addField('exchange_rate')->defaultValue(1);		
		$qsp_master_j->addField('tnc_text')->type('text');		
		$this->addExpression('net_amount_self_currency')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('net_amount'), $m->getElement('exchange_rate')]);
		})->type('money');


		$this->addExpression('round_amount')->set(function($m,$q){
			return "'0'";
		})->type('money');
		//used for the Invoice only
		$qsp_master_j->addField('payment_gateway_id');		
		$qsp_master_j->addField('transaction_reference');
		$qsp_master_j->addField('transaction_response_data');

		$this->getElement('status')->defaultValue('Draft');

		$qsp_master_j->hasMany('xepan\commerce\QSP_Detail','qsp_master_id',null,'Details');
		$qsp_master_j->hasMany('xepan\commerce\QSP_Master','related_qsp_master_id',null,'RelatedQSP');
		

		$this->addHook('beforeDelete',[$this,'deleteDetails']);

		$this->addHook('beforeSave',[$this,'updateTnCTextifChanged']);

		$this->is([
			'contact_id|required',
			'billing_address|required',
			'billing_city|required',
            'billing_state|required',
            'billing_country|required',
            'billing_pincode|required',
            'billing_contact|required',
			'document_no|required|number|unique_in_epan',
			'due_date|required',
			'currency_id|required',
			'exchange_rate|number|gt|0'
			]);
	}

	function updateTnCTextifChanged(){
		if($this->isDirty('tnc_id')){
			$this['tnc_text'] = $this->ref('tnc_id')->get('content');
		}
	}

	function deleteDetails(){
		$deatils = $this->ref('Details');
		foreach ($deatils as $deatil) {
			$deatil->delete();
		}
	}


    //Return qspItem sModel
  	function items(){
		return $this->ref('Details');
	}

	function details(){
		return $this->ref('Details');
	}

	function customer(){
		return $this->ref('contact_id');
	}


	function currency(){
		return $this->add('xepan\accounts\Model_Currency')->load($this['currency_id']);		
	}


	//return tax id and it's total amount
	function getCommnTaxAndAmount(){
		
		if(!$this->loaded())
			throw new \Exception("model must loaded", 1);
		
		$comman_tax_array = [];
		foreach ($this->details() as $invoice_item) {
			if(!$invoice_item['taxation_id'])
				continue;

			// if(in_array($invoice_item['taxation_id'], $comman_tax_array)){
				$comman_tax_array[$invoice_item['taxation']] += $invoice_item['tax_amount'];
			// }
		}

		return $comman_tax_array;
	}


} 