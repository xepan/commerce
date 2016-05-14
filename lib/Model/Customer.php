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

		$cust_j->addField('same_as_billing_address')->type('boolean');
				
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
		$this->addHook('beforeSave',[$this,'updateSearchString']);
		
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
            ->addActivity("Customer '".$this['name']."' is now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	//deactivate Customer
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Customer '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('deactivate','Active',$this);
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

	function updateAddress($billing_detail=[]){
		if(!$this->loaded())
			throw new \Exception("customer not found");
			
		if(!count($billing_detail))
			throw new \Exception("billing or shipping address not found");
		
		$this['billing_address'] = $billing_detail['billing_address'];
		$this['billing_city'] = $billing_detail['billing_city'];
		$this['billing_state'] = $billing_detail['billing_state'];
		$this['billing_country'] = $billing_detail['billing_country'];
		$this['billing_pincode'] = $billing_detail['billing_pincode'];
		
		$this['shipping_address'] = $billing_detail['shipping_address'];
		$this['shipping_city'] = $billing_detail['shipping_city'];
		$this['shipping_state'] = $billing_detail['shipping_state'];
		$this['shipping_pincode'] = $billing_detail['shipping_pincode'];
		$this['shipping_contact'] = $billing_detail['shipping_contact'];
		$this->save();
		return $this;
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['name'];
		$search_string .=" ". str_replace("<br/>", " ", $this['contacts_str']);
		$search_string .=" ". str_replace("<br/>", " ", $this['emails_str']);
		$search_string .=" ". $this['billing_address'];
		$search_string .=" ". $this['billing_city'];
		$search_string .=" ". $this['billing_state'];
		$search_string .=" ". $this['billing_country'];
		$search_string .=" ". $this['billing_pincode'];
		$search_string .=" ". $this['shipping_address'];
		$search_string .=" ". $this['shipping_city'];
		$search_string .=" ". $this['shipping_state'];
		$search_string .=" ". $this['shipping_country'];
		$search_string .=" ". $this['shipping_pincode'];
		$search_string .=" ". $this['pan_no'];
		$search_string .=" ". $this['tin_no'];

		if($this->loaded()){
			$qsp_master = $this->ref('QSPMaster');
			foreach ($qsp_master as $all_qsp_detail) {
				$search_string .=" ". $all_qsp_detail['qsp_master_id'];
				$search_string .=" ". $all_qsp_detail['document_no'];
				$search_string .=" ". $all_qsp_detail['from'];
				$search_string .=" ". $all_qsp_detail['total_amount'];
				$search_string .=" ". $all_qsp_detail['gross_amount'];
				$search_string .=" ". $all_qsp_detail['net_amount'];
				$search_string .=" ". $all_qsp_detail['narration'];
				$search_string .=" ". $all_qsp_detail['exchange_rate'];
				$search_string .=" ". $all_qsp_detail['tnc_text'];
			}
		}

		$this['search_string'] = $search_string;

	}

}
 
    