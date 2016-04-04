<?php

namespace xepan\commerce;

class page_lodgement extends \Page{

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
	
		$transaction_model->addCondition('lodgement_amount','>',0);
	

		$form_transaction = $this->add('Form');
		$v = $this->add('View');
		// $form_transaction->setLayout('view/form/lodgement');

		$transaction_field = $form_transaction->addField('autocomplete/Basic','transaction')->validateNotNull();
		$transaction_field->setModel($transaction_model);

		$transaction_field->other_field->js('change',$form_transaction->js()->submit());
		// $form_transaction->addSubmit('Go');
		if($form_transaction->isSubmitted()){
			$v->js()->reload(['transaction'=>$form_transaction['transaction']])->execute();
		}


		$sale_invoice_model = $this->add('xepan\commerce\Model_SalesInvoice',['title_field'=>'invoice_with_customer']);
		if($selected_transaction_id){

			$selected_trans = $this->add('xepan\accounts\Model_Transaction')->load($selected_transaction_id);
			
			if($selected_trans['related_type'] == "xepan\accounts\Model_Ledger"){
				$sale_invoice_model->addCondition('contact_id',$selected_trans->customer()->id);
			}
			
			$v->add('View')->set("Cr Sum: ".$selected_trans['cr_sum']." Dr Sum: ".$selected_trans['dr_sum']." Lodgged Amount= ".$selected_trans['logged_amount']." Logement Amouont= ".$selected_trans['lodgement_amount']);
		}
		else
			$sale_invoice_model->addCondition('contact_id','-1');

		$form = $v->add('Form',null,null,['form/empty']);
		// $form->setLayout('view/form/lodgement');
		

		$sale_invoice_model->addExpression('invoice_with_customer')->set(function($m,$q){
			return $q->expr('CONCAT([0]," :: ",[1])',[$m->getElement('document_no'), $m->getElement('contact')]);
		});

		$currency = $this->add('xepan\accounts\Model_Currency');
		$count = $sale_invoice_model->count()->getOne();

		$cols = $form->add('Columns');
		$col1 = $cols->addColumn(3)->addStyle(['height'=>'40px','float'=>'left']);
		$col2 = $cols->addColumn(2)->addStyle(['height'=>'40px','float'=>'left']);
		$col3 = $cols->addColumn(2)->addStyle(['height'=>'40px','float'=>'left']);
		$col4 = $cols->addColumn(2)->addStyle(['height'=>'40px','float'=>'left']);
		$col5 = $cols->addColumn(3)->addStyle(['height'=>'40px','float'=>'left']);
		// $count = 5;
		for ($i=1; $i < $count; $i++) {

			$field_invoice = $col1->addField('Dropdown',"invoice_no_".$i);
			$field_invoice->setModel($sale_invoice_model);
			$field_invoice->setEmptyText('Please Select');

			$field_invoice_amount = $col2->addField('line','invoice_amount_'.$i);
			$col3->addField('checkbox','invoice_adjust_'.$i,'Adjust Amount')->set(true);
			$field_invoice_currency = $col4->addField('Dropdown','invoice_currency_'.$i,'Exchange Rate');
			$field_invoice_currency->setModel($currency);

			$col5->addField('line','invoice_exchange_rate_'.$i);

			if($id=$_GET[$field_invoice->name]){
				$field_invoice_amount->set(
					$this->add('xepan\commerce\Model_QSP_Master')
						->load($id)
						->get('net_amount')
						);
				return;
			}


			$field_invoice->js('change',$form->js()->atk4_form(
				'reloadField','invoice_amount_'.$i,
				[
					$this->app->url(null,['cut_object'=>$field_invoice_amount->name]),
					$field_invoice->name=>$field_invoice->js()->val()
				]
				));

		}


		$form->addSubmit('Submit');

		if($form->isSubmitted()){

			for ($i=1; $i < $count; $i++) {

				$field_invoice = "invoice_no_".$i;

				if(!$form[$field_invoice])
					continue;

				$field_invoice_amount = "invoice_amount_".$i;
				$field_invoice_adjust = "invoice_adjust_".$i;
				$field_invoice_currency = "invoice_currency_".$i;
				$field_invoice_exchange_rate = "invoice_exchange_rate_".$i;

				//create transaction for profit or loss
				//mark invoice paid
				//save record into lodgement
				$lodgement_model = $this->add('xepan/commerce/Model_Lodgement');
				$lodgement_model['transaction_id'] = $selected_transaction_id;
				$lodgement_model['salesinvoice_id'] = $form[$field_invoice];
				$lodgement_model['amount'] = $form[$field_invoice_amount];
				$lodgement_model['currency'] = $form[$field_invoice_currency];
				$lodgement_model['exchange_rate'] = $form[$field_invoice_exchange_rate];
				$lodgement_model->save();

			}

		}



	}
} 