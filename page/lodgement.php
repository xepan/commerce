<?php

namespace xepan\commerce;

class page_lodgement extends \xepan\base\Page{

	function init(){
		parent::init();

		$selected_transaction_id = $this->api->stickyGET('transaction');

		//get only those transaction that have lodgement amount and transaction_type in BANK RECEIPT, CASH RECEIPT
		$transaction_type = $this->add('xepan\accounts\Model_TransactionType');

		$transaction_model = $this->add('xepan\accounts\Model_Transaction',['title_field'=>'name_with_amount']);
		
		//load only those transaction where transaction type either 'Bank Recipt' or 'Cash Recipt'
		$transaction_model->addCondition('transaction_type_id',$transaction_type->getReceiptIDs());

		$transaction_model->addExpression('name_with_amount')->set(function($m,$q){
			return $q->expr('CONCAT("Voucher-",[0]," :: Amount -",IF([1],[1],0),"<br/>",[2])',[$m->getElement('voucher_no'), $m->getElement('cr_sum'),$m->getElement('created_at')]);
		});

		$form_transaction = $this->add('Form');
		$v = $this->add('View');
		$transaction_field = $form_transaction->addField('autocomplete/Basic','transaction')->validateNotNull();
		$transaction_field->setModel($transaction_model);

		$transaction_field->other_field->js('change',$form_transaction->js()->submit());
		if($form_transaction->isSubmitted()){
			$v->js()->reload(['transaction'=>$form_transaction['transaction']])->execute();
		}
		
		$lodgement = $this->add('xepan\commerce\Model_Lodgement');
		$v->set($lodgement->do_lodgement($v,$selected_transaction_id));
	}
} 