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
 	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'InActive'=>['view','edit','delete','activate']
					];

	function init(){
		parent::init();

		
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
		
		//TODO Extra Organization Specific Fields other Contacts
		$this->getElement('status')->defaultValue('Active');
		$this->addCondition('type','Customer');
		// $this->addHook('beforeSave',$this);		
		$this->addHook('afterSave',$this);		
		
	}
	function afterSave(){
		$this->account();
	}

	//activate Customer
	function activate(){
		$this['status']='Active';
		$this->saveAndUnload();
	}

	//deactivate Customer
	function deactivate(){
		$this['status']='InActive';
		$this->saveAndUnload();
	}

	function account(){
		$account = $this->add('xepan\accounts\Model_Account')
				->addCondition('contact_id',$this->id)
				->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadSundryDebtor()->fieldQuery('id'));
		$account->tryLoadAny();
		if(!$account->loaded()){
			$account['name'] = $this['name'];
			$account['AccountDisplayName'] = $this['name'];
			$account->save();
		}else{
			$account['name'] = $this['name'];
			$account['updated_at'] = $this->app->now;
			$account->save();
		}

		return $account;

	}
}
 
    