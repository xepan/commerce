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

		// $this->addField('exchange_amount')->type('money')->defaultValue(0);
	}
}
 
    