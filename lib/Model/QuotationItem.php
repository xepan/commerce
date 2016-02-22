<?php

namespace xepan\commerce;

class Model_QuotationItem extends \xepan\base\Model_Table{
	
	function init(){
		parent::init();

		$quotationitem_j = $this->join('Quotation.document_id');
		$quotationitem_j = $this->join('Quotation.document_stauts');

		$quotationitem_j->hasmany('xepan\commerce\quotaiton');

		$quotationitem_j->hasOne('xepan\marketing\Lead\lead_id');
		$quotationitem_j->hasOne('xepan\marketing\Opportunity\opportunity_id');
		$quotationitem_j->hasOne('xepan\commerce\CustomerProfile\customer_id');
		$quotationitem_j->hasOne('xepan\commerce\CustomerProfile\customer_image');
		$quotationitem_j->hasOne('xepan\commerce\Currency\currency_id');

		$quotationitem_j->addField('no')->mandatory(true);
		$quotationitem_j->addField('total_amount')->type('money');
		$quotationitem_j->addField('tax')->type('money');
		$quotationitem_j->addField('gross_amount');
		$quotationitem_j->addField('net_amount')->type('money')->mandatory(true);

		

	}
}
