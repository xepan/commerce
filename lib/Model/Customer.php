<?php

/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


 namespace xepan\commerce;

 class Model_Customer extends \xepan\base\Model_Contact{
 	public $status = ['Active','InActive','Communication'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','communication'],
					'InActive'=>['view','edit','delete','activate','communication']
					];

	function init(){
		parent::init();

		$this->getElement('created_by_id');//->defaultValue($this->app->employee->id);

		$cust_j=$this->join('customer.contact_id');
		$cust_j->hasOne('xepan\accounts\Currency','currency_id');
		// $cust_j->hasOne('Users','users_id')->mandatory(true)->sortable(true);
		
		$cust_j->addField('billing_address')->type('text');
		$cust_j->addField('billing_city');
		$cust_j->addField('billing_state');
		$cust_j->addField('billing_country');
		$cust_j->addField('billing_pincode');
		
		
		$cust_j->addField('shipping_address')->type('text');
		$cust_j->addField('shipping_city');
		$cust_j->addField('shipping_state');
		$cust_j->addField('shipping_country');
		$cust_j->addField('shipping_pincode');
		$cust_j->addField('tin_no');
		$cust_j->addField('pan_no');

		$this->hasMany('xepan/commerce/Model_QSP_Master',null,null,'QSPMaster');
		$this->hasMany('xepan/comerce/Model_Designer_Image_Category','contact_id');
		
		$this->addExpression('customer_currency_icon',function($m,$q){
			return $m->refSQL('currency_id')->fieldQuery('icon');
		});

		//TODO Extra Organization Specific Fields other Contacts
		$this->getElement('status')->defaultValue('Active');
		$this->addCondition('type','Customer');
		$this->addHook('afterSave',$this);	
		$this->addHook('beforeDelete',[$this,'checkQSPExistance']);	
		
	}

	function checkQSPExistance($m){
		$customer_qsp_count = $m->ref('QSPMaster')->count()->getOne();
		
		if($customer_qsp_count){
			throw new \Exception("First delete the invoice/order/.. of this customer");
			
		}	
	}

	function afterSave(){
		$this->app->hook('customer_update',[$this]);
	}

	//activate Customer
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("InActive Customer", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('activate','InActive');
		$this->save();
	}

	//deactivate Customer
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Active Customer", $this->id /*Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('deactivate','Active');
		return $this->save();
	}

	function ledger(){
		$account = $this->add('xepan\accounts\Model_Ledger')
				->addCondition('contact_id',$this->id)
				->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadSundryDebtor()->fieldQuery('id'));
		$account->tryLoadAny();
		if(!$account->loaded()){
			$account['name'] = $this['name'];
			$account['LedgerDisplayName'] = $this['name'];
			$account['ledger_type'] = 'Customer';
			$account->save();
		}else{
			$account['name'] = $this['name'];
			$account['updated_at'] = $this->app->now;
			$account->save();
		}

		return $account;

	}
}
 
    