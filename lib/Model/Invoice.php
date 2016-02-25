<?php

namespace xepan\commerce;

class Model_Invoice extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$invoice_j = $this->join('invoice.document_id');

		$invoice_j->hasOne('xepan\commerce\Customer','customer_id');
		$invoice_j->hasOne('xepan\commerce\TermsAndCondition','termsandcondition_id');
		$invoice_j->hasOne('xepan\commerce\Currency','curerncy_id');
		$invoice_j->hasOne('xepan\commerce\DiscountVoucher','discount_voucher_id');

		$invoice_j->addField('shipping_address')->type('text');
		// BUG add shipping_{city,state,country,pincode}
		$invoice_j->addField('billing_address')->type('text');
		// BUG add billing_{city,state,country,pincode}

		$invoice_j->addField('name')->caption('Invoice No');


		$invoice_j->addField('gross_amount')->type('money');
		
		$invoice_j->addField('discount_amount')->type('money');
		$invoice_j->addField('discount_percentage')->type('money');
		$invoice_j->addField('grand_total')->type('money');
		
		$invoice_j->addField('vat_amount')->type('money')->mandatory(true);
		$invoice_j->addField('net_amount')->type('money');

		$invoice_j->hasMany('xepan\commerce\InvoiceItem','');

	}
}
 