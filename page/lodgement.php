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


		$sale_invoice_model = $this->add('xepan\commerce\Model_SalesInvoice',['title_field'=>'invoice_with_customer']);
		if($selected_transaction_id){

			$selected_trans = $this->add('xepan\accounts\Model_Transaction')->load($selected_transaction_id);
			
			if($selected_trans['related_type'] == "xepan\accounts\Model_Ledger"){
				$sale_invoice_model->addCondition('contact_id',$selected_trans->customer()->id);
				$sale_invoice_model->addCondition('currency_id',$selected_trans['currency_id']);
				$sale_invoice_model->addCondition('status',"Due");
			}
			

			$v->add('View')->set("Cr Sum: ".$selected_trans['cr_sum']." Dr Sum: ".$selected_trans['dr_sum']." Lodgged Amount= ".$selected_trans['logged_amount']." Logement Amount= ".$selected_trans['lodgement_amount']." Currency= ".$selected_trans['currency']." Exchange Rate= ".$selected_trans['exchange_rate']);
		}
		else
			$sale_invoice_model->addCondition('contact_id','-1');

		$form = $v->add('Form',null,null,['form/empty']);

		$count = $sale_invoice_model->count()->getOne();

		$cols = $form->add('Columns');
		$col1 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'10%']);
		$col2 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'10%']);
		$col3 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'10%']);
		$col4 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'10%']);
		$col5 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'10%']);
		$col6 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'15%']);
		$col7 = $cols->addColumn(1)->addStyle(['height'=>'40px','float'=>'left','width'=>'20%']);

		$col1->add('View')->setElement('b')->set('id');
		$col2->add('View')->setElement('b')->set('number');
		$col3->add('View')->setElement('b')->set('amount');
		$col4->add('View')->setElement('b')->set('currency');
		$col5->add('View')->setElement('b')->set('rate');
		$col6->add('View')->setElement('b')->set('adjust amount');
		$col7->add('View')->setElement('b')->set('Profit/Loss');
		
		$total_invoice_count = $sale_invoice_model->count()->getOne();

		$transaction_amount_adjust = 0;
		$i = 1;	
		foreach ($sale_invoice_model as $junk) {
			$col1->addField('Line',"invoice_id_".$i)->set($junk['id']);

			$field_invoice = $col2->addField('Line',"invoice_no_".$i);
			$field_invoice->set($junk['document_no']);

			$field_invoice_amount = $col3->addField('line','invoice_amount_'.$i)->set($junk['net_amount']);

			$field_invoice_currency = $col4->addField('Line','invoice_currency_'.$i,'Exchange Rate');	
			$field_invoice_currency->set($junk['currency']);

			$field_invoice_exchange_rate = $col5->addField('line','invoice_exchange_rate_'.$i);
			$field_invoice_exchange_rate->set($junk['exchange_rate']);

			$field_adjust_amount = $col6->addField('Line','invoice_adjust_'.$i,'Adjust Amount')->set(true);
			
			// adjust transaction amount remaining lodgement amount			
			$adjust = 0;
			if($selected_trans['lodgement_amount'] > $transaction_amount_adjust){
				$transaction_amount_adjust += $junk['net_amount'];
				if($selected_trans['lodgement_amount'] > $transaction_amount_adjust ){
					$adjust = $junk['net_amount'];
				}else if($selected_trans['lodgement_amount'] - ($transaction_amount_adjust - $junk['net_amount']) > 0){
					$adjust = $selected_trans['lodgement_amount'] - ($transaction_amount_adjust - $junk['net_amount']);
				}else
				$adjust = 0;
			}
			$field_adjust_amount->set($adjust);


			$field_profit_loss = $col7->addField('line','invoice_gain_loss_'.$i,'Profit/Loss');

			$according_invoice_exchange_amount = $junk['exchange_rate'] * $adjust;
			$according_transaction_exchange_amount = $selected_trans['exchange_rate'] * $adjust;
			
			if($_GET[$field_adjust_amount->name.'_amount']){
				$amount = $_GET[$field_adjust_amount->name.'_amount'];
				$according_invoice_exchange_amount = $_GET['invoice_exchange_rate'] * $amount;
				$according_transaction_exchange_amount = $_GET['transaction_exchange_rate'] * $amount;

			}

			
			$field_profit_loss->set($according_transaction_exchange_amount - $according_invoice_exchange_amount);

			// on change of adjusted amount reset profit_loss field

			$field_adjust_amount->js('change',$field_profit_loss->js()->reload([$this->app->url(),$field_adjust_amount->name.'_amount'=>$field_adjust_amount->js()->val(), 'invoice_exchange_rate'=>$junk['exchange_rate'],'transaction_exchange_rate'=>$selected_trans['exchange_rate']   ]));

			$i++;
		}


		$form->addSubmit('Submit')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			try{
				$this->app->db->beginTransaction();
				for ($i=1; $i <= $total_invoice_count; $i++) {

					$field_invoice_id = "invoice_id_".$i;
					$field_invoice_no = "invoice_no_".$i;
					$field_invoice_amount = "invoice_amount_".$i;
					$field_invoice_currency = "invoice_currency_".$i;
					$field_invoice_exchange_rate = "invoice_exchange_rate_".$i;

					$field_adjust_amount = "invoice_adjust_".$i;
					$field_profit_loss = "invoice_gain_loss_".$i;

					$lodge_array = array('invoice_id' =>$form[$field_invoice_id] ,
										'invoice_no'=>$form[$field_invoice_no] ,
										'invoice_amount'=>$form[$field_invoice_amount],
										'invoice_currency'=>$form[$field_invoice_currency],
										'invoice_exchange_rate'=>$form[$field_invoice_exchange_rate],
										'invoice_adjust'=>$form[$field_adjust_amount],
										'invoice_gain_loss'=>$form[$field_profit_loss]);
					if(!$form[$field_adjust_amount])
						continue;

					$lodgement = $this->add('xepan\commerce\Model_Lodgement');
					$lodgement->do_lodgement($lodge_array,$selected_transaction_id,$selected_trans);
					
				}
				
				$this->app->db->commit();
					$form->js(null,$form->js()->reload())->univ()->successMessage('Lodgement Successfully')->execute();
			}catch(\Exception $e){
				$this->app->db->rollback();
				throw $e;
			}
		}
	}
} 