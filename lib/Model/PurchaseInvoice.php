<?php

namespace xepan\commerce;

class Model_PurchaseInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Due','Paid'];
	public $actions = [

    'Draft'=>['view','edit','delete','submit','manage_attachments'],
    'Submitted'=>['view','edit','delete','approve','manage_attachments','print_document'],
    'Due'=>['view','edit','delete','paid','manage_attachments','print_document'],
    'Paid'=>['view','edit','delete','manage_attachments','print_document']
    ];

	// public $acl = false;

    function init(){
      parent::init();

      $this->addCondition('type','PurchaseInvoice');
      $this->getElement('document_no')->defaultValue($this->newNumber());

    }


    function submit(){
        $this['status']='Submitted';
        $this->app->employee
        ->addActivity("Purchase Invoice no. '".$this['document_no']."' has submitted", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('approve','Submitted',$this);
        $this->saveAndUnload();
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
        ->addActivity("Purchase Invoice no. '".$this['document_no']."' due for rs. '".$this['net_amount']."' ", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('paid','Due',$this);
        $this->updateTransaction();
        $this->saveAndUnload();
    }

    function paid(){
        $this['status']='Paid';
        $this->app->employee
        ->addActivity("Amount '".$this['net_amount']."' of purchase invoice no. '".$this['document_no']."' has been paid", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/,null,null,"xepan_commerce_purchaseinvoicedetail&document_id=".$this->id."")
        ->notifyWhoCan('delete','Paid',$this);
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
            $discount_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Discount Recieved");
            $new_transaction->addCreditLedger($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);

                //Load Round Ledger
            $round_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Round Account");
            $new_transaction->addCreditLedger($discount_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);

                //CR
                //Load Purchase Ledger
            $purchase_ledger = $this->add('xepan\accounts\Model_Ledger')->load("Purchase Account");
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
                $tax_model = $this->add('xepan\commerce\Model_Taxation')->load($tax_id);
                $tax_ledger = $tax_model->ledger();
                $new_transaction->addDebitLedger($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate']);
            }


            $new_transaction->execute();
        }
    }

    function addItem($item,$qty,$price,$sale_amount,$original_amount,$shipping_charge,$shipping_duration,$express_shipping_charge=null,$express_shipping_duration=null,$narration=null,$extra_info=null,$taxation_id=null,$tax_percentage=null){
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

}
