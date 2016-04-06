<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','approve','manage_attachments'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Due'=>['view','edit','delete','redesign','reject','paid','send','cancel','manage_attachments'],
				'Paid'=>['view','edit','delete','send','cancel','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments']
				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','SalesInvoice');
		
		$nominal_field = $this->getField('nominal_id');
		$nominal_field->mandatory(true);

		$sale_group = $this->add('xepan\accounts\Model_Group')->loadSalesAccount();
		$model = $nominal_field->getModel();
		
		$model->addCondition(
							$model->dsql()->orExpr()
								->where('root_group_id',$sale_group->id)
								->where('parent_group_id',$sale_group->id)
								->where('id',$sale_group->id)
						);

		$this->addHook('beforeDelete',[$this,'deleteTransactions']);

	}

	function draft(){
		$this['status']='Draft';
        $this->app->employee
            ->addActivity("Draft QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('submit','Submitted');
        $this->saveAndUnload();
    }

    function approve(){
		$this['status']='Due';
        $this->app->employee
            ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('redesign,reject,send','Submitted');
        $this->updateTransaction();
        $this->saveAndUnload();
    }

    function cancel(){
    	$this['status']='Canceled';
        // $this->app->employee
        //     ->addActivity("Due QSP", $this->id Related Document ID, $this['contact_id'] /*Related Contact ID*/)
        //     ->notifyWhoCan('send','Due');
        $this->deleteTransactions();
        $this->saveAndUnload();
    }

    function paid(){
		$this['status']='Paid';
        $this->app->employee
            ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('send','Due');
        $this->saveAndUnload();
    }


    function PayViaOnline($transaction_reference,$transaction_reference_data){
		$this['transaction_reference'] =  $transaction_reference;
	    $this['transaction_response_data'] = json_encode($transaction_reference_data);
	    $this->save();
	}

	function deleteTransactions(){
		$old_transaction = $this->add('xepan\accounts\Model_Transaction');
		$old_transaction->addCondition('related_id',$this->id);
		$old_transaction->addCondition('related_type',"xepan\commerce\Model_SalesInvoice");

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
			//saleinvoice model transaction have always one entry in transaction
			$this->deleteTransactions();
		}

		if($create_new){
			$new_transaction = $this->add('xepan\accounts\Model_Transaction');
			$new_transaction->createNewTransaction("SalesInvoice",$this,$this['created_at'],'Sale Invoice',$this->currency(),$this['exchange_rate'],$this['id'],'xepan\commerce\Model_SalesInvoice');
					
			//DR
			//Load Party Ledger
			$customer_ledger = $this->add('xepan\accounts\Model_Ledger')->loadCustomerLedger($this['contact_id']);
			$new_transaction->addDebitAccount($customer_ledger,$this['net_amount'],$this->currency(),$this['exchange_rate']);
			// echo "Dr-Customer-net_amount-".$this['net_amount']."<br/>";		
			//Load Discount Ledger
			$discount_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultDiscountAccount();
			$new_transaction->addDebitAccount($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);
			// echo "Dr-Customer-discount_amount-".$this['discount_amount']."<br/>";		
				
			//Load Round Ledger
			$round_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultRoundAccount();
			$new_transaction->addDebitAccount($discount_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);
			// echo "Dr-Customer-rount_amount-".$this['round_amount']."<br/>";		


			//CR
			//Load Sale Ledger
			$sale_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultSalesAccount();
			$new_transaction->addCreditAccount($sale_ledger, $this['total_amount'], $this->currency(), $this['exchange_rate']);
			// echo "cr-Customer-gross_amount-".$this['total_amount']."<br/>";		

			// //Load Multiple Tax Ledger according to sale invoice item
			$comman_tax_array = [];
			foreach ($this->details() as $invoice_item) {
				if( $invoice_item['taxation_id'] and !in_array( trim($invoice_item['taxation']), $comman_tax_array)){
					$comman_tax_array[$invoice_item['taxation_id']] += $invoice_item['tax_amount'];
				}
			}

			foreach ($comman_tax_array as $tax_id => $total_tax_amount ) {
				// echo "common tax id =  ".$tax_id."Value = ".$total_tax_amount;
				$tax_model = $this->add('xepan\commerce\Model_Taxation')->load($tax_id);
				$tax_ledger = $this->add('xepan\accounts\Model_Ledger')->loadTaxLedger($tax_model);
				$new_transaction->addCreditAccount($tax_ledger, $total_tax_amount, $this->currency(), $this['exchange_rate']);
			}

			
			$new_transaction->execute();
		}
	}

	function addItem($item,$qty,$price,$shipping_charge,$narration=null,$extra_info=null){
		$in_item = $this->ref('Details');
		$in_item['item_id'] = $item->id;
		$in_item['quantity'] = $qty;
		$in_item['price'] = $price;
		$in_item['shipping_charge'] = $shipping_charge;
		$in_item['narration'] = $narration;
		$in_item['extra_info'] = $extra_info;
		$in_item->save();
	}

}
