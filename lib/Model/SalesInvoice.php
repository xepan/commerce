<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','due','manage_attachments'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Due'=>['view','edit','delete','redesign','reject','paid','send','manage_attachments'],
				'Paid'=>['view','edit','delete','send','manage_attachments'],
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
	}

	function draft(){
		$this['status']='Draft';
        $this->app->employee
            ->addActivity("Draft QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('submit','Submitted');
        $this->saveAndUnload();
    }

    function due(){
		$this['status']='Due';
        $this->app->employee
            ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('redesign,reject,send','Submitted');
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

	function updateTransaction(){
		if(!$this->loaded())
			throw new \Exception("model must loaded for updating transaction");
			
		$old_transaction = $this->add('xepan\accounts\Model_Transaction_Sale');
		$old_transaction->addCondition('related_id',$this->id);

		if($old_transaction->count()->getOne()){
			$old_transaction->tryLoadAny();
			$old_transaction->deleteTransactionRow();
			$old_transaction->delete();
		}

		
		$new_transaction = $this->add('xepan\accounts\Model_Transaction_Sale');
		$new_transaction->createNewTransaction("SalesInvoice",$this,$this['created_at'],'Sale Invoice',$this->currency(),$this['exchange_rate'],$this['id'],'xepan\commerce\Model_SalesInvoice');
		
		//CR
		//Load Party Ledger
		$customer_ledger = $this->add('xepan\accounts\Model_Ledger')->loadCustomerLedger($this['customer_id']);
		$new_transaction->addDebitAccount($customer_ledger,$this['net_amount'],$this->currency(),$this['exchange_rate']);
		//Load Discount Ledger
		$discount_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultDiscountAccount();
		$new_transaction->addDebitAccount($discount_ledger,$this['discount_amount'],$this->currency(),$this['exchange_rate']);
			
		//Load Round Ledger
		$round_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultRoundAccount();
		$new_transaction->addDebitAccount($discount_ledger,$this['round_amount'],$this->currency(),$this['exchange_rate']);

		//DR
		//Load Sale Ledger
		$sale_ledger = $this->add('xepan\accounts\Model_Ledger')->loadDefaultSalesAccount();
		$new_transaction->addCreditAccount($sale_ledger, $this['gross_amount'], $this->currency(), $this['exchange_rate']);

		//Load Multiple Tax Ledger according to sale invoice item
		$
		foreach ($this->items() as $item) {
				
		}

		$new_transaction->addCreditAccount($account, $amount, $Currency=null, $exchange_rate=1.00);

		$new_transaction->execute();
	}

}
