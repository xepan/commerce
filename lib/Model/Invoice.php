<?php

namespace xepan\commerce;

class Model_Invoice extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$invoice_j = $this->join('invoice.document_id');
		$invoice_j = $this->join('invoice.document_createddate');

		$invoice_j->hasmany('xepan\commerce\invoiceitem');

		$invoice_j->addField('shipping_address')->type('text');
		$invoice_j->addField('billing_address')->type('text');

		$invoice_j->addField('name')->caption('Invoice No');

		$invoice_j->hasOne('xepan\commerce\TermsAndCondition\termsandcondition_id');
		$invoice_j->hasOne('xepan\commerce\currency_id');

		$invoice_j->addField('qty')->sortable(true);

		$invoice_j->addField('tax')->type('money');
		$invoice_j->addField('tax_amount')->type('money');
		$invoice_j->addField('unitprice')->type('money');
		$invoice_j->addField('total')->type('money')->mandatory(true);

		$invoice_j->addField('sub_total')->type('money');
		$invoice_j->addField('discount')->type('money');
		$invoice_j->addField('total')->type('money');
		$invoice_j->addField('vat_percentage')->type('rate')->mandatory(true);
		$invoice_j->addField('grand_total')->type('money');

	}
}
 