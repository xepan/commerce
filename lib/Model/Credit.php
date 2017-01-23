<?php

 namespace xepan\commerce;

 class Model_Credit extends \xepan\base\Model_Table{
 	public $table="credit";

 	public $acl_type = "CustomerCredit";
 	public $actions = ['All'=>['view','edit','delete']];
	
	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id)->system(true);
		$this->hasOne('xepan\commerce\Model_Customer','customer_id','organization_name')->display(array('form'=>'autocomplete/Basic'));
		$this->hasOne('xepan\commerce\Model_SalesInvoice','sale_invoice_id');
		$this->hasOne('xepan\commerce\Model_SalesOrder','sale_order_id');
		$this->addField('name');
		$this->addField('type')->enum(['add','consumed'])->defaultValue('add');
		$this->addField('amount')->type('money');

		$this->is([
					'customer_id|required',
					'name|required',
					'type|required',
					'amount|required'
				]);
	}

	function consume($customer_id,$sale_order,$amount){

		$this['customer_id'] = $customer_id;
		if($invoice = $sale_order->invoice())
			$this['sale_invoice_id'] = $invoice->id;

		$this['sale_order_id'] = $sale_order->id;
		$this['type'] = "consumed";
		$this['amount'] = $amount;
		$this['name'] = "Amount ".$this['amount']." created on behalf of order ".$sale_order['document_no'];
		return $this->save();
	}
}