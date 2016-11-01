<?php

/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

 namespace xepan\commerce;

 class Model_CustomerCredit extends \xepan\commerce\Model_Customer{

	function init(){
		parent::init();

		$this->addExpression('total_credit',function($m,$q){
			$credit = $m->add('xepan\commerce\Model_Credit')
								->addCondition('customer_id',$m->getElement('id'))
								->addCondition('type','add');

			return $q->expr('IFNULL([0],0)',[$credit->sum('amount')]);
		})->type('money');

		$this->addExpression('total_consumed',function($m,$q){
			$credit	= $m->add('xepan\commerce\Model_Credit')
						->addCondition('customer_id',$m->getElement('id'))
						->addCondition('type','consumed');

			return $q->expr('IFNULL([0],0)',[$credit->sum('amount')]);
		})->type('money');

		$this->addExpression('remaining_credit_amount')->set(function($m,$q){
			return $q->expr('[total_credit] - [total_consumed]',
							[
								'total_credit'=>$m->getElement('total_credit'),
								'total_consumed'=>$m->getElement('total_consumed')
							]);
		})->type('money');

	}

	function consumeCredit($amount,$order){
		if(!$this->loaded())
			throw new \Exception("Customer not loaded");			
		
		$credit = $this->add('xepan\commerce\Model_Credit');
		$credit->consume($this->id,$order,$amount);

	}
}