<?php

 namespace xepan\commerce;

 class Model_InvoiceTransactionAssociation extends \xepan\base\Model_Table{
 	public $table="invoice_transaction_association";
 	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_SalesInvoice','salesinvoice_id');
		$this->hasOne('xepan\accounts\Model_Transaction','transaction_id');

		$this->addField('amount')->type('money');
		$this->addField('currency');
		$this->addField('exchange_rate')->type('money');
		$this->addField('exchange_amount')->type('money');

	}
}
 
    