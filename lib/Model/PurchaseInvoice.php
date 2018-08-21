<?php

namespace xepan\commerce;

class Model_PurchaseInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Due','Paid'];
	public $actions = [

    'Draft'=>['view','edit','delete','cancel','submit','other_info','manage_attachments','communication'],
    'Submitted'=>['view','edit','delete','cancel','redesign','approve','other_info','manage_attachments','print_document','communication'],
    'Redesign'=>['view','edit','delete','cancel','submit','other_info','manage_attachments','communication'],
    'Canceled'=>['view','edit','delete','redraft','other_info','manage_attachments','communication'],
    'Due'=>['view','edit','delete','cancel','redesign','send','paid','cancel','other_info','manage_attachments','print_document','communication'],
    'Paid'=>['view','edit','delete','cancel','send','other_info','manage_attachments','print_document','communication']
    ];

	// public $acl = false;
    public $document_type = "PurchaseInvoice";
    public $addOtherInfo = true;

    function init(){
      parent::init();

      $this->addCondition('type','PurchaseInvoice');
      $this->getElement('document_no');//->defaultValue($this->newNumber());

      $this->is([
      'document_no|required'
      ]);

      $this->addHook('beforeDelete',[$this,'deleteTransactions']);
      $this->addHook('beforeSave',[$this,'checkDocumentNo']);

      $this->addHook('afterSave',[$this,'checkUpdateTransaction']);
    }

    function checkUpdateTransaction(){
        if(in_array($this['status'], ['Due','Paid']))
            $this->updateTransaction();
    }

    function checkDocumentNo(){

        $purchaseinvoice_m = $this->add('xepan\commerce\Model_PurchaseInvoice');
        $purchaseinvoice_m->addCondition('contact_id',$this['contact_id']);
        $purchaseinvoice_m->addCondition('document_no',$this['document_no']);
        // Allow self editing
        $purchaseinvoice_m->addCondition('id','<>',$this->id);
        $purchaseinvoice_m->tryLoadAny();
        if($purchaseinvoice_m->loaded())
            throw $this->exception('Puchase invoice number already in use for '. $this['contact'],'ValidityCheck')->setField('document_no');
    }

    function deleteTransactions(){
        $old_transaction = $this->add('xepan\accounts\Model_Transaction');
        $old_transaction->addCondition('related_id',$this->id);
        $old_transaction->addCondition('related_type',"xepan\commerce\Model_PurchaseInvoice");

        // For avoid the cash & bank type of transaction 
        $old_transaction->addCondition('transaction_template_id',null);
        
        $old_amount = 0;
        $old_transaction->tryLoadAny();
        if($old_transaction->loaded()){
            $old_amount = $old_transaction['cr_sum_exchanged'];
            $old_transaction->deleteTransactionRow();
            $old_transaction->delete();
        }

        return $old_amount;
    }


    function submit(){
        $this['status'] = 'Submitted';
        $this->app->employee
        ->addActivity("Purchase Invoice No : '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('approve','Submitted',$this);
        $this->save();
    }

    function print_document(){
        $this->print_QSP();
    }

    function page_send($page){
        $this->send_QSP($page,$this);
    }

    function approve(){

        $this['status']='Due';
        $this->app->employee
        ->addActivity("Purchase Invoice No : '".$this['document_no']."' being due for '".$this['currency']." ".$this['net_amount']."' ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('paid','Due',$this);
        // $this->updateTransaction();
        $this->save();
    }

    function redraft(){
        $this['status']='Draft';
        $this->app->employee
        ->addActivity("Purchase Invoice No : '".$this['document_no']."' redraft", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('submit','Draft',$this);
        $this->save();
    }

    function redesign(){
        $this['status']='Redesign';
        $this->app->employee
        ->addActivity("Purchase Invoice No : '".$this['document_no']."' proceed for redesign ", $this->id /*Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('submit','Redesign',$this);
        $this->deleteTransactions();
        $this->save();
    }
    
    function cancel($reason=null,$narration=null){
        $this['status']='Canceled';
        if($reason)
            $this['cancel_reason'] = $reason;
        if($narration)
            $this['cancel_narration'] = $narration;

        $this->app->employee
            ->addActivity("Purchase Invoice No : '".$this['document_no']."' canceled & proceed for redraft ", $this->id /*Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
            ->notifyWhoCan('delete,redraft','Canceled');
        $this->deleteTransactions();
        $this->save();
    }

    function paid(){
        $this['status']='Paid';
        $this->app->employee
        ->addActivity("Amount : ' ".$this['net_amount']." ".$this['currency']." ' Paid , against Purchase Invoice No : '".$this['document_no']."'", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('delete','Paid',$this);
        $this->save();
    }

    function page_paid($page){

        $tabs = $page->add('Tabs');
        $cash_tab = $tabs->addTab('Cash Payment');
        $bank_tab = $tabs->addTab('Bank Payment');
        $adjust_tab = $tabs->addTab('Adjust Amounts');

        $ledger = $this->supplier()->ledger();
        $pre_filled =[
            1 => [
                'party' => ['ledger'=>$ledger,'amount'=>$this['net_amount'],'currency'=>$this->ref('currency_id')]
            ]
        ];

        $et = $this->add('xepan\accounts\Model_EntryTemplate');
        $et->loadBy('unique_trnasaction_template_code','PARTYCASHPAYMENT');
        
        $et->addHook('afterExecute',function($et,$transaction,$total_amount,$row_data){
            $lodgement = $this->add('xepan\commerce\Model_Lodgement');
            $output = $lodgement->doLodgement(
                    [$this->id],
                    $transaction[0]->id,
                    $row_data[0]['rows']['party']['amount'],
                    $row_data[0]['rows']['cash']['currency']?:$this->app->epan->default_currency->id,
                    $row_data[0]['rows']['cash']['exchange_rate']?:1,
                    "PurchaseInvoice"
                );
            $this->app->page_action_result = $et->form->js()->univ()->closeDialog();
        });

        $view_cash = $cash_tab->add('View');
        $et->manageForm($view_cash,$this->id,'xepan\commerce\Model_PurchaseInvoice',$pre_filled);

        $et_bank = $this->add('xepan\accounts\Model_EntryTemplate');
        $et_bank->loadBy('unique_trnasaction_template_code','PARTYBANKPAYMENT');
        
        $et_bank->addHook('afterExecute',function($et_bank,$transaction,$total_amount,$row_data){
            $lodgement = $this->add('xepan\commerce\Model_Lodgement');
            $output = $lodgement->doLodgement(
                    [$this->id],
                    $transaction[0]->id,
                    $row_data[0]['rows']['party']['amount'],
                    $row_data[0]['rows']['party']['currency']?:$this->app->epan->default_currency->id,
                    $row_data[0]['rows']['party']['exchange_rate']?:1.0,
                    "PurchaseInvoice"
                );
            $this->app->page_action_result = $et_bank->form->js()->univ()->closeDialog();
        });
        $view_bank = $bank_tab->add('View');
        $et_bank->manageForm($view_bank,$this->id,'xepan\commerce\Model_PurchaseInvoice',$pre_filled);

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
                                    $unlodged_tra_model->getElement('dr_sum'),
                                    $unlodged_tra_model->getElement('unlogged_amount')
                                ])
                            );
        $tr_row_j = $unlodged_tra_model->join('account_transaction_row.transaction_id');
        $ledger_j = $tr_row_j->leftJoin('ledger');
        $ledger_j->addField('tr_contact_id','contact_id');
        
        $unlodged_tra_model->addCondition('tr_contact_id',$this->supplier()->id);
        $unlodged_tra_model->addCondition('party_currency_id',$this['currency_id']);
        $unlodged_tra_model->addCondition('transaction_type',['BankPaid','CashPaid']);
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
        $view->set("Invoice Amount to Lodged = ".$invoice_unlogged_amount);


        $form->addSubmit("Adjust");
        if($form->isSubmitted()){

            $u_tra_model = $this->add('xepan\accounts\Model_Transaction')->load($form['unlodged_transaction']);
            
            $row_model = $this->add('xepan\accounts\Model_TransactionRow')
                        ->addCondition('transaction_id',$u_tra_model->id)
                        ->addCondition('ledger_id',$this->supplier()->ledger()->id)
                        ->tryLoadAny();         

            $output = $adjust_tab->add('xepan\commerce\Model_Lodgement')
                        ->doLodgement(
                                        [$this->id],
                                        $form['unlodged_transaction'],
                                        $u_tra_model['unlogged_amount'],
                                        $u_tra_model['currency_id'],
                                        $row_model['exchange_rate'],
                                        "PurchaseInvoice"
                                    );
            if($output[$this->id]['status'] == "success"){
                return $this->app->page_action_result = $form->js()->univ()->closeDialog();
            }
            $this->app->page_action_result = $form->js()->reload();
        }

    }

    function supplier(){
        return $this->add('xepan\commerce\Model_Supplier')->tryLoad($this['contact_id']);
    }

    function updateTransaction($delete_old=true,$create_new=true){

        if(!$this->loaded())
            throw new \Exception("model must loaded for updating transaction");

        if(!in_array($this['status'], ['Due','Paid']))          
            return;
        if($delete_old){  
            $old_amount = $this->deleteTransactions();
        }

        // to track and adjust debit and credit must be same kind of error
        $cr_sum=0;
        $dr_sum=0;
        $item_nominal=[];
        $total_nominal_amount = 0;

        if($create_new){
            $new_transaction = $this->add('xepan\accounts\Model_Transaction');
            $new_transaction->createNewTransaction("PurchaseInvoice",$this,$this['created_at'],'Purchase Invoice '.$this['document_no'],$this->currency(),$this['exchange_rate'],$this['id'],'xepan\commerce\Model_PurchaseInvoice');

                //DR
                //Load Party Ledger
            $supplier_ledger = $this->add('xepan\commerce\Model_Supplier')->load($this['contact_id'])->ledger();

            $new_transaction->addCreditLedger($supplier_ledger,$this['net_amount'],$this->currency(),$this['exchange_rate']);
            $cr_sum += $this['net_amount'];
            // echo "CR Sum ".$this['net_amount']."<br/>";

                //Load Discount Ledger
            $discount_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Rebate & Discount Received");
            $new_transaction->addCreditLedger($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);
            $cr_sum += $this['discount_amount'];
            // echo "CR Sum2 ".$this['discount_amount']."<br/>";

            // //Load Multiple Tax Ledger according to purchase invoice item
            $comman_tax_array = [];
            foreach ($this->details() as $invoice_item) {
                if( $invoice_item['taxation_id']){
                    if($invoice_item['sub_tax']){
                        $sub_taxs = explode(",", $invoice_item['sub_tax'])?:[];

                        foreach ($sub_taxs as $sub_tax) {
                            $sub_tax_detail = explode("-", $sub_tax);
                            $sub_tax_id = $sub_tax_detail[0];
                            if(!in_array($sub_tax_id, array_keys($comman_tax_array)))
                                $comman_tax_array[$sub_tax_id] = 0;
                            //calculate sub tax amount of form item tax amount
                            //claculate first percentage from tax percentag
                            $sub_tax_amount = ((($sub_tax_detail[2] /$invoice_item['tax_percentage'])*100.00 ) * $invoice_item['tax_amount']) / 100.00;
                            $comman_tax_array[$sub_tax_id] += round($sub_tax_amount,2);
                        }
                    }else{
                        if(!in_array( trim($invoice_item['taxation_id']), array_keys($comman_tax_array)))
                            $comman_tax_array[$invoice_item['taxation_id']]= 0;
                        $comman_tax_array[$invoice_item['taxation_id']] += round($invoice_item['tax_amount'],2);
                    }
                }

                //item nominal -----------
                if($invoice_item['item_purchase_nominal_id']){
                    if($invoice_item['amount_excluding_tax_and_shipping']){
                        if(!isset($item_nominal[$invoice_item['item_purchase_nominal_id']])) $item_nominal[$invoice_item['item_purchase_nominal_id']] = 0;

                        $item_nominal[$invoice_item['item_purchase_nominal_id']] += $invoice_item['amount_excluding_tax_and_shipping'];
                        $total_nominal_amount += $invoice_item['amount_excluding_tax_and_shipping'];
                    }
                }
                //end item nominal -----------
            }

            $dr_sum += $total_nominal_amount;

            foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
                $dr_sum += $total_tax_amount;
                // echo "DR Sum2 ".$total_tax_amount."<br/>";
            }

            //Load Round Ledger
            $round_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Round Account");
            if($this['round_amount'] < 0){
                $new_transaction->addDebitLedger($round_ledger,abs($this['round_amount']),$this->currency(),$this['exchange_rate']);
                $dr_sum += abs($this['round_amount']);
                // echo "DR Sum3 ".$this['round_amount']."<br/>";
            }
            else{                
                $new_transaction->addCreditLedger($round_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);
                $cr_sum += abs($this['round_amount']);
                // echo "CR Sum ".$this['round_amount']."<br/>";
            }


            //DR nominal transaction
            foreach ($item_nominal as $nominal_id => $nominal_value) {
                //Load Ledger
                if($total_nominal_amount && $this['nominal_id'] == $nominal_id && !$nominal_value ) continue;
                $nominal_ledger = $this->add('xepan\accounts\Model_Ledger')->loadBy('id',$nominal_id);
                $new_transaction->addDebitLedger($nominal_ledger, $nominal_value, $this->currency(), $this['exchange_rate']);
            }

            //CR
            //Load Purchase Ledger
            $purchase_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Purchase Account");
            $new_transaction->addDebitLedger($purchase_ledger, $cr_sum - $dr_sum, $this->currency(), $this['exchange_rate']);
            // echo "Purchase Ledger Sum ".($cr_sum - $dr_sum)."<br/>";


            foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
                $tax_model = $this->add('xepan\commerce\Model_Taxation')->load($tax_id);
                $tax_ledger = $tax_model->ledger();
                $new_transaction->addDebitLedger($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate'],$tax_model['sub_tax']);
                // echo "Tax Ledger Sum ".($total_tax_amount)."<br/>";
            }

            $new_amount = $new_transaction->execute();
        }


        // Automated invoice lodgement and status changed
        $invoice_old = $this->add('xepan\commerce\Model_PurchaseInvoice');
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
        // if(isset($new_amount) && $old_amount != $new_amount){   
        //     if($this['status']=='Paid'){
        //         $this['status']='Due';
        //         $this->save();
        //     }
        // }
    }

    function removeLodgement(){
        if(!$this->loaded()) throw new \Exception("Invoice Must be Loaded", 1);
        $inv_lodg = $this->add('xepan\commerce\Model_Lodgement')
                         ->addCondition('invoice_id',$this->id);
        $inv_lodg->deleteAll();
    }
    
    function addItem($item,$qty,$price,$sale_amount,$original_amount,$shipping_charge,$shipping_duration,$express_shipping_charge=null,$express_shipping_duration=null,$narration=null,$extra_info=null,$taxation_id=null,$tax_percentage=null,$qty_unit_id){
        if(!$this->loaded())
            throw new \Exception("PurchaseInvoice must loaded", 1);

        if(!$taxation_id and $tax_percentage){
            $tax = $item->applicableTaxation();
            $taxation_id = $tax['taxation_id'];
            $tax_percentage = $tax['tax_percentage'];
        }

        $in_item = $this->add('xepan\commerce\Model_QSP_Detail')->addCondition('qsp_master_id',$this->id);
        $in_item['item_id'] = $item->id;
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

    function puchaseOrder(){
        if(!$this->loaded())
            throw new \Exception("purchase invoice must loaded", 1);
        if(!$this['related_qsp_master_id'])
            throw new \Exception("Related order not found", 1);
        
        $purchaseorder = $this->add('xepan\commerce\Model_PurchaseOrder')->tryLoad($this['related_qsp_master_id']);

        if(!$purchaseorder->loaded())
            throw new \Exception("Related order not found", 1);         

        return $purchaseorder;
    }

    function transactionRemoved($app,$transaction){
        $inv_m = $this->add('xepan\commerce\Model_PurchaseInvoice');
        $inv_m->load($transaction['related_id']);
        if($inv_m['status']=="Paid"){
            $inv_m['status']="Due";
            $inv_m->save();
        }
    }

}
