<?php

namespace xepan\commerce;

class Model_PurchaseInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Due','Paid'];
	public $actions = [

    'Draft'=>['view','edit','delete','submit','manage_attachments'],
    'Submitted'=>['view','edit','delete','approve','manage_attachments','can_print_document'],
    'Due'=>['view','edit','delete','paid','manage_attachments','can_print_document'],
    'Paid'=>['view','edit','delete','manage_attachments','can_print_document']
    ];

	// public $acl = false;

    function init(){
      parent::init();

      $this->addCondition('type','PurchaseInvoice');

    }

    function submit(){
        $this['status']='Submitted';
        $this->app->employee
        ->addActivity("Draft QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
        ->notifyWhoCan('submit','Draft');
        $this->saveAndUnload();
    }   

    function can_print_document(){
        $this->print_Document();
    }

    function approve(){

        $this['status']='Due';
        $this->app->employee
        ->addActivity("Approved QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
        ->notifyWhoCan('redesign,reject,send','Submitted');
        $this->updateTransaction();
        $this->saveAndUnload();
    }

    function due(){
        $this['status']='Due';
        $this->app->employee
        ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
        ->notifyWhoCan('redesign,reject,send','Approved');
        $this->saveAndUnload();
    }

    function paid(){
        $this['status']='Paid';
        $this->app->employee
        ->addActivity("Paid QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
        ->notifyWhoCan('send','Due');
        $this->saveAndUnload();
    }


    function deleteTransactions(){
        $old_transaction = $this->add('xepan\accounts\Model_Transaction');
        $old_transaction->addCondition('related_id',$this->id);
        $old_transaction->addCondition('related_type',"xepan\commerce\Model_PurchaseInvoice");

        if($old_transaction->count()->getOne()){
            $old_transaction->tryLoadAny();
            $old_transaction->deleteTransactionRow();
            $old_transaction->delete();
        }
    }


    function updateTransaction($delete_old=true,$create_new=true){

        if(!$this->loaded())
            throw new \Exception("model must loaded for updating transaction");

        if(!in_array($this['status'], ['Due','Paid']))          
            return;
        if($delete_old){  

            $this->deleteTransactions();
        }

        if($create_new){
            $new_transaction = $this->add('xepan\accounts\Model_Transaction');
            $new_transaction->createNewTransaction("PurchaseInvoice",$this,$this['created_at'],'Purchase Invoice',$this->currency(),$this['exchange_rate'],$this['id'],'xepan\commerce\Model_PurchaseInvoice');

                //DR
                //Load Party Ledger
            $supplier_ledger = $this->add('xepan\commerce\Model_Supplier')->load($this['contact_id'])->ledger();

            $new_transaction->addCreditLedger($supplier_ledger,$this['net_amount'],$this->currency(),$this['exchange_rate']);

                //Load Discount Ledger
            $discount_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultDiscountRecieveLedger();
            $new_transaction->addCreditLedger($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);

                //Load Round Ledger
            $round_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultRoundLedger();
            $new_transaction->addCreditLedger($discount_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);

                //CR
                //Load Purchase Ledger
            $purchase_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultPurchaseLedger();
            $new_transaction->addDebitLedger($purchase_ledger, $this['total_amount'], $this->currency(), $this['exchange_rate']);

                // //Load Multiple Tax Ledger according to sale invoice item
            $comman_tax_array = [];
            foreach ($this->details() as $invoice_item) {
                if( $invoice_item['taxation_id']){
                    if(!in_array( trim($invoice_item['taxation_id']), array_keys($comman_tax_array)))
                        $comman_tax_array[$invoice_item['taxation_id']]= 0;
                    $comman_tax_array[$invoice_item['taxation_id']] += $invoice_item['tax_amount'];
                }
            }

            foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
                    // echo "common tax id =  ".$tax_id."Value = ".$total_tax_amount;
                $tax_model = $this->add('xepan\commerce\Model_Taxation')->load($tax_id);
                $tax_ledger = $tax_model->ledger();
                $new_transaction->addDebitLedger($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate']);
            }


            $new_transaction->execute();
        }
    }

}
