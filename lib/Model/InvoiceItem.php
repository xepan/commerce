<?php

namespace xepan\commerce;

class Model_InvoiceDetail extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$invoiceitem_j = $this->join('invoice.document_id');

		$invoiceitem_j->hasOne('xepan\commere\Invoice','invoice_id');

		$invoiceitem_j = $this->join('Quotation.document_id');
		$invoiceitem_j = $this->join('Quotation.document_stauts');

		$invoiceitem_j->hasone('xepan\commerce\invoice');

		$invoiceitem_j->hasOne('xepan\commerce\Currency','currency_id');
		$invoiceitem_j->hasOne('xepan\commerce\item_id');

		$invoiceitem_j->hasOne('xepan\marketing\Opportunity\opportunity_id');
		$invoiceitem_j->hasOne('xepan\commerce\CustomerProfile\customer_name');
		$invoiceitem_j->hasOne('xepan\commerce\CustomerProfile\customer_contactno');
		$invoiceitem_j->hasOne('xepan\commerce\CustomerProfile\customer_image');
		$invoiceitem_j->hasOne('xepan\commerce\Currency\currency_id');

		$invoiceitem_j->addField('no')->mandatory(true);
		$invoiceitem_j->addField('price')->type('money');
		

	}
}
 