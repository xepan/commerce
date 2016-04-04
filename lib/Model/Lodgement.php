<?php

//InvoiceTransactionAssociation
 namespace xepan\commerce;

 class Model_Lodgement extends \xepan\base\Model_Table{
 	public $table="lodgement";
 	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Model_SalesInvoice','salesinvoice_id');
		$this->hasOne('xepan\accounts\Model_Transaction','transaction_id');

		$this->addField('amount')->type('money')->defaultValue(0);
		$this->addField('currency');
		$this->addField('exchange_rate')->type('money');

		$this->addExpression('exchange_amount')->set(function($m,$q){
			return $q->expr('([0]*[1])',[$m->getElement('amount'), $m->getElement('exchange_rate')]);
		})->type('money');

		$this->addExpression('logged_amount')->set(function($m,$q){
			$lodge_model = $m->add('xepan\commerce\Model_Lodgement')
						->addCondition('transaction_id',$q->getField('id'));
			return $lodge_model->sum($q->expr('IFNULL([0],0)',[$lodge_model->getElement('exchange_amount')]));
		})->type('money');

		$this->addExpression('lodgement_amount')->set(function($m,$q){
			return $q->expr("([0]-IF([1],[1],0))",[$m->getElement('cr_sum'),$m->getElement('logged_amount')]);
		})->type('money');

		// $this->addField('exchange_amount')->type('money')->defaultValue(0);

	}
}
 
    