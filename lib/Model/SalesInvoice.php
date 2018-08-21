<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
		'Draft'=>['view','cancle','edit','delete','submit','other_info','manage_attachments','communication'],
		'Submitted'=>['view','cancle','edit','delete','other_info','redesign','approve','manage_attachments','print_document','communication'],
		'Redesign'=>['view','edit','delete','submit','other_info','cancle','manage_attachments','communication'],
		'Due'=>['view','edit','delete','redesign','paid','send','cancel','other_info','manage_attachments','print_document','communication'],
		'Paid'=>['view','edit','delete','send','cancel','other_info','manage_attachments','print_document','communication'],
		'Canceled'=>['view','edit','delete','redraft','other_info','manage_attachments','communication']
		];

	public $document_type = 'SalesInvoice';
	public $addOtherInfo = true;

	function init(){
		parent::init();

		$this->addCondition('type','SalesInvoice');
		$this->getElement('document_no');//->defaultValue($this->newNumber());
		
		$nominal_field = $this->getField('nominal_id');
		$nominal_field->defaultValue($this->add('xepan\accounts\Model_Ledger')->load('Sales Account')->get('id'));

		$sale_group = $this->add('xepan\accounts\Model_Group')->load("Sales");
		$sale_group->addCondition(
			$sale_group->dsql()->orExpr()
			->where('root_group_id',$sale_group->id)
			->where('parent_group_id',$sale_group->id)
			->where('id',$sale_group->id)
			);
		$model = $nominal_field->getModel();
		$model->addCondition('group_id',$sale_group->id);
		
		$this->addHook('beforeDelete',[$this,'notifyDeletion']);
		$this->addHook('beforeDelete',[$this,'deleteTransactions']);
		$this->addHook('beforeDelete',[$this,'removeLodgement']);
		$this->addHook('afterSave',[$this,'checkUpdateTransaction']);
		
		// $this->is([
		// 	'document_no|required|number'
		// 	]);

	}

	// qsp_saving_from_pos used for bypassing the function double calling, because at pos page update function is called manually
	function checkUpdateTransaction(){
		if(in_array($this['status'], ['Due','Paid']) AND (!isset($this->app->qsp_saving_from_pos)) ){
			$this->updateTransaction();
		}
	}

	function print_document(){
		$this->print_QSP();
	}

	function page_send($page){
		$this->send_QSP($page,$this);
	}

	function redesign(){
		$this['status']='Redesign';
		$this->app->employee
		->addActivity("Sales Invoice No : '".$this['document_no']."' proceed for redesign", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('submit','Redesign',$this);
		$this->deleteTransactions();
		$this->save();
	}

	function redraft(){
		$this['status']='Draft';
		$this->app->employee
		->addActivity("Sales Invoice No : '".$this['document_no']."' redraft", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('submit','Draft',$this);
		$this->save();
	}


	function approve(){
		if(!$this['document_no'] || $this['document_no']=='-') {
			$this['document_no']=$this->newNumber();
			$this['created_at'] = $this->app->now;
		}
		$this['status']='Due';
		$this->app->employee
		->addActivity("Sales Invoice No : '".$this['document_no']."' being due for '".$this['currency']." ".$this['net_amount']."' ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('redesign,paid,send,cancel','Due',$this);
		// $this->updateTransaction();
		$this->save();

		$this->app->hook('invoice_approved',[$this]);
	}

	function cancel($reason=null,$narration=null){
		$this['status']='Canceled';
		if($reason)
			$this['cancel_reason'] = $reason;
		if($narration)
			$this['cancel_narration'] = $narration;
		
        $this->app->employee
            ->addActivity("Sales Invoice No : '".$this['document_no']."' canceled & proceed for redraft ", $this->id /*Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
            ->notifyWhoCan('delete,redraft','Canceled');
		$this->deleteTransactions();
		$this->save();
	}

	function submit(){
		$this['status']='Submitted';
		$this->app->employee
		->addActivity("Sales Invoice No : '".$this['document_no']."' has submitted", $this->id, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('approve,reject','Submitted');
		$this->save();
	}

	function page_paid($page){
		$header_view = $page->add('View')->addClass('alert alert-info');

		$tabs = $page->add('Tabs');
		$cash_tab = $tabs->addTab('Cash Reveived');
		$bank_tab = $tabs->addTab('Bank Received');
		$adjust_tab = $tabs->addTab('Adjust Amounts');

		$ledger = $this->customer()->ledger();

		$pre_filled =[
			'CashReceipt' => [
				'party' => ['ledger'=>$ledger,'amount'=>$this['net_amount'],'currency'=>$this->ref('currency_id')]
			],
			'BankReceipt' => [
				'party' => ['ledger'=>$ledger,'amount'=>$this['net_amount'],'currency'=>$this->ref('currency_id')]
			],
			
		];
		
		$et = $this->add('xepan\accounts\Model_EntryTemplate');
		$et->loadBy('unique_trnasaction_template_code','PARTYCASHRECEIVED');
		
		$et->addHook('afterExecute',function($et,$transaction,$total_amount,$row_data){
			$lodgement = $this->add('xepan\commerce\Model_Lodgement');
			$output = $lodgement->doLodgement(
					[$this->id],
					$transaction[0]->id,
					$row_data[0]['rows']['party']['amount'],
					$row_data[0]['rows']['cash']['currency']?:$this->app->epan->default_currency->id,
					$row_data[0]['rows']['cash']['exchange_rate']?:1
				);
			$this->app->page_action_result = $et->form->js()->univ()->closeDialog();
		});

		// $v = $page->add('View',null,null,['view/accountsform/amtrecevied']);
		$view_cash = $cash_tab->add('View');
		$et->manageForm($view_cash,$this->id,'xepan\commerce\Model_SalesInvoice',$pre_filled);

		$et_bank = $this->add('xepan\accounts\Model_EntryTemplate');
		$et_bank->loadBy('unique_trnasaction_template_code','PARTYBANKRECEIVED');
		
		$et_bank->addHook('afterExecute',function($et_bank,$transaction,$total_amount,$row_data){
			$lodgement = $this->add('xepan\commerce\Model_Lodgement');
			$output = $lodgement->doLodgement(
					[$this->id],
					$transaction[0]->id,
					$row_data[0]['rows']['party']['amount'],
					$row_data[0]['rows']['party']['currency']?:$this->app->epan->default_currency->id,
					$row_data[0]['rows']['party']['exchange_rate']?:1.0,
					"SalesInvoice"
				);
			$this->app->page_action_result = $et_bank->form->js()->univ()->closeDialog();
		});
		
		$view_bank = $bank_tab->add('View');
		$et_bank->manageForm($view_bank,$this->id,'xepan\commerce\Model_SalesInvoice',$pre_filled);

		//Adjust Amount
		$form = $adjust_tab->add('Form');
		$unlodged_tra_field = $form->addField('xepan\base\DropDown','unlodged_transaction')->validate('required');
		
		$unlodged_tra_model = $adjust_tab->add('xepan\accounts\Model_Transaction')->addCondition('unlogged_amount','>',0);
		$unlodged_tra_model->title_field = "adjustment_title";
		$unlodged_tra_model->addExpression('adjustment_title')
							->set(
								$unlodged_tra_model->dsql()->expr('CONCAT([0]," [ Total Amount= ",[1]," ] [ Unlodgged Amount = ",[2]," ]")',
								[
									$unlodged_tra_model->getElement('created_at'),
									$unlodged_tra_model->getElement('cr_sum'),
									$unlodged_tra_model->getElement('unlogged_amount')
								])
							);
		$tr_row_j = $unlodged_tra_model->join('account_transaction_row.transaction_id');
		$ledger_j = $tr_row_j->join('ledger');
		$ledger_j->addField('tr_contact_id','contact_id');
		
		$unlodged_tra_model->addCondition('tr_contact_id',$this->customer()->id);
		$unlodged_tra_model->addCondition('party_currency_id',$this['currency_id']);
		$unlodged_tra_model->addCondition('transaction_type',['BankReceipt','CashReceipt']);
		$unlodged_tra_model->dsql()->group('id');

		$unlodged_tra_model->setOrder('created_at','desc');
		$unlodged_tra_field->setModel($unlodged_tra_model);
		
		$view = $form->add('View_Info');
		$invoice_lodge = $view->add('xepan\commerce\Model_Lodgement')->addCondition('invoice_id',$this->id);
		$invoice_unlogged_amount = 0;
		foreach ($invoice_lodge as $obj) {
			$invoice_unlogged_amount += $obj['amount'];
		}
		$invoice_unlogged_amount = $this['net_amount'] - $invoice_unlogged_amount;

		$advanced_amount = $unlodged_tra_model->sum('unlogged_amount');
		$header_view->setHtml('Customer: <b>'.$this->add('xepan\base\Model_Contact')->tryLoad($this['contact_id'])['name_with_type'].'</b><br/> Invoice Total Amount: <b>'.$this['net_amount']."</b><br/>Customer Unlodgged Advanced Amount: <b>".$advanced_amount."</b><br/>Invoice Due Amount: <b>".$invoice_unlogged_amount."<b/>");


		$form->addSubmit("Adjust");
		if($form->isSubmitted()){

			$u_tra_model = $this->add('xepan\accounts\Model_Transaction')->load($form['unlodged_transaction']);
			
			$row_model = $this->add('xepan\accounts\Model_TransactionRow')
						->addCondition('transaction_id',$u_tra_model->id)
						->addCondition('ledger_id',$this->customer()->ledger()->id)
						->tryLoadAny();			

			$output = $adjust_tab->add('xepan\commerce\Model_Lodgement')
						->doLodgement(
										[$this->id],
										$form['unlodged_transaction'],
										$u_tra_model['unlogged_amount'],
										$u_tra_model['currency_id'],
										$row_model['exchange_rate'],
										"SalesInvoice"
									);
			if($output[$this->id]['status'] == "success"){
				return $this->app->page_action_result = $form->js()->univ()->closeDialog();
			}
			$this->app->page_action_result = $form->js()->reload();
		}

	}

	function paid(){
		$this['status']='Paid';
		$this->app->employee
		->addActivity(" Amount : ' ".$this['net_amount']." ".$this['currency']." ' Recieved, against Sales Invoice No : '".$this['document_no']."'", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('send,cancel','Paid');
		$this->save();
		$this->app->hook('invoice_paid',[$this]);
	}


	function PayViaOnline($transaction_reference,$transaction_reference_data){
		$this['transaction_reference'] =  $transaction_reference;
		$this['transaction_response_data'] = json_encode($transaction_reference_data);
		$this->save();
		$this->paid();
	}

	function notifyDeletion(){
		$this->app->employee
		->addActivity("Sales Invoice No : '".$this['document_no']."' deleted of amount '".$this['net_amount']."' ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_salesinvoicedetail&document_id=".$this->id."")
		->notifyWhoCan('approve,reject','Submitted');
	}

	function deleteTransactions(){
		$old_transaction = $this->add('xepan\accounts\Model_Transaction');
		$old_transaction->addCondition('related_id',$this->id);
		$old_transaction->addCondition('related_type',"xepan\commerce\Model_SalesInvoice");
		
		// For avoid the cash & bank type of transaction 
		$old_transaction->addCondition('transaction_template_id',null);

		$old_voucher_no = null;
		$old_transaction->tryLoadAny();
		if($old_transaction->loaded()){
			// $old_amount = $old_transaction['cr_sum_exchanged'];
			$old_voucher_no = $old_transaction['name'];
			$old_transaction->deleteTransactionRow();
			$old_transaction->delete();
		}

		// return $old_amount;
		return $old_voucher_no;
	}

	function updateTransaction($delete_old=true,$create_new=true){
		if(!$this->loaded())
			throw new \Exception("model must loaded for updating transaction");
		
		if(!in_array($this['status'], ['Due','Paid']))			
			return;

		$old_voucher_no = null;
		if($delete_old){			
			//saleinvoice model transaction have always one entry in transaction
			$old_voucher_no = $this->deleteTransactions();
		}

		// to track and adjust debit and credit must be same kind of error
		$cr_sum=0;
		$dr_sum=0;

		if($create_new){
			$new_transaction = $this->add('xepan\accounts\Model_Transaction');
			$new_transaction->createNewTransaction("SalesInvoice",$this,$this['created_at'],'Sale Invoice '.$this['serial']." ".$this['document_no'],$this->currency(),$this['exchange_rate'],$this['id'],'xepan\commerce\Model_SalesInvoice');

			if($old_voucher_no){				
				$new_transaction['name'] = $old_voucher_no;
			}
			//DR
			//Load Party Ledger
			$customer_ledger = $this->add('xepan\commerce\Model_Customer')->load($this['contact_id'])->ledger();
			
			$new_transaction->addDebitLedger($customer_ledger,$this['net_amount'],$this->currency(),$this['exchange_rate']);
			$dr_sum += $this['net_amount'];
			
			//Load Discount Ledger
			$discount_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Rebate & Discount Allowed");
			$new_transaction->addDebitLedger($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);
			$dr_sum += $this['discount_amount'];

			// //Load Multiple Tax Ledger according to sale invoice item
			$comman_tax_array = [];
			// item based nominal id
			$item_nominal = [];
			$total_nominal_amount = 0;
			$total_shipping_amount = 0;

			// $this->app->print_r($this->details()->getRows());
			// $this->app->print_r($this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id)->getRows(),true);

			foreach ($this->details() as $invoice_item) {

				// tax calculation------------
				if( $invoice_item['taxation_id']){
					
					//calculating sub tax amount
					if($invoice_item['sub_tax']){
						$sub_taxs = explode(",", $invoice_item['sub_tax'])?:[];

						foreach ($sub_taxs as $sub_tax) {
							$sub_tax_detail = explode("-", $sub_tax);
							$sub_tax_id = $sub_tax_detail[0];
							if(!in_array($sub_tax_id, array_keys($comman_tax_array)))
								$comman_tax_array[$sub_tax_id] = 0;
							//calculate sub tax amount of form item tax amount
							//claculate first percentage from tax percentag
							if($invoice_item['tax_percentage'] > 0)
								$sub_tax_amount = ((($sub_tax_detail[2] /$invoice_item['tax_percentage'])*100 ) * $invoice_item['tax_amount']) / 100;
							else
								$sub_tax_amount = 0;		
							$comman_tax_array[$sub_tax_id] += round($sub_tax_amount,2);
						}

					}else{
						if(!in_array( trim($invoice_item['taxation_id']), array_keys($comman_tax_array)))
							$comman_tax_array[$invoice_item['taxation_id']]= 0;
						$comman_tax_array[$invoice_item['taxation_id']] += round($invoice_item['tax_amount'],2);
					}

				}
				// end of tax calculation 

				//item nominal -----------
				if($invoice_item['item_nominal_id']){
					if($invoice_item['amount_excluding_tax_and_shipping']){
						if(!isset($item_nominal[$invoice_item['item_nominal_id']])) $item_nominal[$invoice_item['item_nominal_id']] = 0;

						$item_nominal[$invoice_item['item_nominal_id']] += $invoice_item['amount_excluding_tax_and_shipping'];
						$total_nominal_amount += $invoice_item['amount_excluding_tax_and_shipping'];
					}
				}
				//end item nominal -----------

				// shipping amount-----
				$total_shipping_amount += $invoice_item['shipping_amount'];
			}
			
			foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
				$cr_sum += $total_tax_amount;
			}
			
			//CR Load Round Ledger
			$round_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Round Account");
			if($this['round_amount'] < 0){
				$new_transaction->addCreditLedger($round_ledger,abs($this['round_amount']),$this->currency(),$this['exchange_rate']);
				$cr_sum += abs($this['round_amount']);
			}
			else{
				$new_transaction->addDebitLedger($round_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);
				$dr_sum += $this['round_amount'];
			}

			$cr_sum += $total_nominal_amount;
			if(!isset($item_nominal[$this['nominal_id']])){
				$item_nominal[$this['nominal_id']] = 0;
			}
			
			$cr_sum += $total_shipping_amount;

			$add_sale_nominal = count($item_nominal);
			// master nominal
			$item_nominal[$this['nominal_id']] += ($dr_sum - $cr_sum);

			//CR nominal transaction
			foreach ($item_nominal as $nominal_id => $nominal_value) {
				//Load Ledger
				if($total_nominal_amount && $this['nominal_id'] ==$nominal_id && !$nominal_value ) continue;
				$nominal_ledger = $this->add('xepan\accounts\Model_Ledger')->loadBy('id',$nominal_id);
				$new_transaction->addCreditLedger($nominal_ledger, $nominal_value, $this->currency(), $this['exchange_rate']);	
			}
			// $sale_ledger->addCondition('id',$this['nominal_id']);

			//CR tax transaction
			foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
				$tax_model = $this->add('xepan\commerce\Model_Taxation')->tryLoad($tax_id);
				if(!$tax_model->loaded()) continue;
				
				$tax_ledger = $tax_model->ledger();
				$new_transaction->addCreditLedger($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate'],$tax_model['sub_tax']);
			}
			
			//CR shipping ledegr transaction
			if($total_shipping_amount > 0){
				$shipping_ledger = $this->add('xepan\accounts\Model_Ledger')->loadBy('name',"Shipping Account");
				$new_transaction->addCreditLedger($shipping_ledger, $total_shipping_amount, $this->currency(), $this['exchange_rate']);
			}

			$new_amount = $new_transaction->execute();
		}
		
		// Automated invoice lodgement and status changed
		$invoice_old = $this->add('xepan\commerce\Model_SalesInvoice');
		$invoice_old->addExpression('logged_amount')->set(function($m,$q){
			$lodge_model = $m->add('xepan\commerce\Model_Lodgement')->addCondition('invoice_id',$q->getField('id'));
			return $lodge_model->sum($q->expr('IFNULL([0],0)',[$lodge_model->getElement('amount')]));
		})->type('money');
		$invoice_old->load($this->id);
		
		if($invoice_old['logged_amount'] && $invoice_old['logged_amount'] > $this['net_amount']){
			$this->removeLodgement();
			if($this['status']=='Paid'){
				$this['status']='Due';
				$this->save();
			}
		}elseif($invoice_old['logged_amount'] && $invoice_old['logged_amount'] == $this['net_amount']){
			$this['status']='Paid';
			$this->save();
		}
		
	}

	function removeLodgement(){
		if(!$this->loaded()) throw new \Exception("Invoice Must be Loaded", 1);
		$inv_lodg = $this->add('xepan\commerce\Model_Lodgement')
						 ->addCondition('invoice_id',$this->id);
		$inv_lodg->deleteAll();
	}

	function addItem($item,$qty,$price,$sale_amount,$original_amount,$shipping_charge,$shipping_duration,$express_shipping_charge=null,$express_shipping_duration=null,$narration=null,$extra_info=null,$taxation_id=null,$tax_percentage=null,$qty_unit_id){
		if(!$this->loaded())
			throw new \Exception("SalesInvoice must loaded", 1);

		if(!($item instanceof \xepan\commerce\Model_Item) and is_numeric($item)){
			$item = $this->add('xepan\commerce\Model_Item')->load($item);
		}

		if(!$taxation_id and $tax_percentage){
			$tax = $item->applicableTaxation();
			$taxation_id = $tax['taxation_id'];
			$tax_percentage = $tax['tax_percentage'];
		}

		$in_item = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
		// if($item instanceof \xepan\commerce\Model_Item)
			$in_item['item_id'] = $item->id;
		// else
		// 	$in_item['item_id'] = $item;

		$in_item['qsp_master_id'] = $this->id;
		$in_item['quantity'] = $qty;
		$in_item['price'] = $price;
		$in_item['shipping_charge'] = $shipping_charge;
		$in_item['shipping_duration'] = $shipping_duration;
		$in_item['sale_amount'] = $sale_amount;
		$in_item['original_amount'] = $original_amount;
		$in_item['shipping_duration'] = $shipping_duration;
		$in_item['express_shipping_charge'] = $express_shipping_charge;
		$in_item['express_shipping_duration'] = $express_shipping_duration;
		$in_item['narration'] = $narration;
		$in_item['extra_info'] = $extra_info;
		$in_item['taxation_id'] = $taxation_id;
		$in_item['tax_percentage'] = $tax_percentage;
		$in_item['qty_unit_id'] = $qty_unit_id;

		$in_item->save();

	}

	function saleOrder(){
		if(!$this->loaded())
			throw new \Exception("sale invoice must loaded", 1);
		
		if(!$this['related_qsp_master_id'])
			return false;
		
		$saleorder = $this->add('xepan\commerce\Model_SalesOrder')->tryLoad($this['related_qsp_master_id']);

		if(!$saleorder->loaded())
			return false;

		return $saleorder;
	}


	function customer(){
		return $this->add('xepan\commerce\Model_Customer')->tryLoad($this['contact_id']);
	}

	function generateInvoiceFromPOS($status='Due', $customer_id=null){
		
		$customer = $this->add('xepan\commerce\Model_Customer')->load($customer_id);
		
		$tnc_model = $this->add('xepan\commerce\Model_TNC')->addCondition('is_default_for_sale_invoice',true)->tryLoadAny();

		// $invoice = $this->add('xepan\commerce\Model_SalesInvoice');
		// $invoice->addCondition('related_qsp_master_id',$this->id);
		// $invoice->tryLoadAny();

		$this['contact_id'] = $customer->id;
		$this['currency_id'] = $customer['currency_id']?$customer['currency_id']:$this->app->epan->default_currency->get('id');
		// $invoice['related_qsp_master_id'] = $this->id;
		$this['tnc_id'] = $tnc_model?$tnc_model->id:null;
		$this['tnc_text'] = $tnc_model?$tnc_model['content']:null;
		
		$this['status'] = $status;

		$due_date = $this->app->now;
		$this['due_date'] = $due_date;
		$this['exchange_rate'] = $this['exchange_rate'];

		$this['document_no'] = $this['document_no'];

		$this['billing_address'] = $customer['billing_address'];
		$this['billing_city'] = $customer['billing_city'];
		$this['billing_state_id'] = $customer['billing_state_id'];
		
		$this['billing_country_id'] = $customer['billing_country_id'];
		$this['billing_pincode'] = $customer['billing_pincode'];
		
		$this['shipping_address'] = $customer['shipping_address'];
		$this['shipping_city'] = $customer['shipping_city'];
		$this['shipping_state_id'] = $customer['shipping_state_id'];
		$this['shipping_country_id'] = $customer['shipping_country_id'];
		$this['shipping_pincode'] = $customer['shipping_pincode'];
		$this->save();
		return $this;
	}

}
