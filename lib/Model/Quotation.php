<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$quotation_j = $this->join('Quotation.document_id');
		$quotation_j = $this->join('Quotation.document_createddate');

		$quotation_j->hasmany('xepan\commerce\quotaiton');
		$quotation_j->hasOne('xepan\commerce\CustomerProfile\customer_id');
		$quotation_j->hasOne('xepan\commerce\CustomerProfile\customer_address');
		$quotation_j->hasOne('xepan\commerce\TermsAndCondition\termsandcondition_id');
		$quotation_j->hasOne('xepan\commerce\Currency\currency_id');

		$quotation_j->hasOne('commerce/Quotation','quotation_id');

		$quotation_j->addField('qty')->sortable(true);

		$quotation_j->addField('tax')->type('money');
		$quotation_j->addField('tax_amount')->type('money');
		$quotation_j->addField('unitprice')->type('money');
		$quotation_j->addField('total')->type('money')->mandatory(true);

		$quotation_j->addField('sub_total')->type('money');
		$quotation_j->addField('discount')->type('money');
		$quotation_j->addField('total')->type('money');
		$quotation_j->addField('vat_percentage')->type('rate')->mandatory(true);
		$quotation_j->addField('grand_total')->type('money');
		


		
	}
}
