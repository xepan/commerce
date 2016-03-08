<?php

namespace xepan\commerce;

class Model_QSP_Detail extends \Model_Table{

	public $status = [
						];
	public $actions = [
						];
	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\QSP_Master','qsp_master_id');

		$this->addField('price');
		$this->addField('quantity');

		$this->addExpression('amount_excluding_tax')->set('ROUND(price*quantity,2)');

		$this->addField('tax_percentage');

		$this->addExpression('tax_amount')->set('ROUND(price*tax_percentage*quantity/100.00,2)');

		$this->addExpression('total_amount')->set(function($m,$q){

			return $q->expr('[0]+[1]',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);

		});

		$this->addField('narration');
		$this->addField('extra_info');

	}
}