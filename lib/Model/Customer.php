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
		$cust_j->hasOne('xepan\base\Country','billing_country_id')->display(array('form' => 'xepan\commerce\DropDown'));
		$cust_j->hasOne('xepan\base\State','billing_state_id')->display(array('form' => 'xepan\commerce\DropDown'));
		$cust_j->hasOne('xepan\base\Country','shipping_country_id')->display(array('form' => 'xepan\commerce\DropDown'));
		$cust_j->hasOne('xepan\base\State','shipping_state_id')->display(array('form' => 'xepan\commerce\DropDown'));
		// $cust_j->hasOne('Users','users_id')->mandatory(true)->sortable(true);
		
		$cust_j->addField('billing_address')->type('text');
		$cust_j->addField('billing_city');
		$cust_j->addField('billing_pincode');

		$cust_j->addField('same_as_billing_address')->type('boolean');
				
		$cust_j->addField('shipping_address')->type('text');
		$cust_j->addField('shipping_city');
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
            ->addActivity("Customer '".$this['name']."' is now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	//deactivate Customer
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Customer '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('activate','InActive',$this);
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

	function updateAddress($address_detail=[]){
		if(!$this->loaded())
			throw new \Exception("customer not found");
			
		if(!count($address_detail))
			throw new \Exception("billing or shipping address not found");
		
		$this['billing_address'] = $address_detail['billing_address'];
		$this['billing_city'] = $address_detail['billing_city'];
		$this['billing_state_id'] = $address_detail['billing_state_id'];
		$this['billing_country_id'] = $address_detail['billing_country_id'];
		$this['billing_pincode'] = $address_detail['billing_pincode'];
		
		$this['shipping_address'] = $address_detail['shipping_address'];
		$this['shipping_city'] = $address_detail['shipping_city'];
		$this['shipping_state_id'] = $address_detail['shipping_state_id'];
		$this['shipping_country_id'] = $address_detail['shipping_country_id'];
		$this['shipping_pincode'] = $address_detail['shipping_pincode'];
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
		$search_string .=" ". $this['billing_state_id'];
		$search_string .=" ". $this['billing_country_id'];
		$search_string .=" ". $this['billing_pincode'];
		$search_string .=" ". $this['shipping_address'];
		$search_string .=" ". $this['shipping_city'];
		$search_string .=" ". $this['shipping_state_id'];
		$search_string .=" ". $this['shipping_country_id'];
		$search_string .=" ". $this['shipping_pincode'];
		$search_string .=" ". $this['pan_no'];
		$search_string .=" ". $this['tin_no'];
		$search_string .=" ". $this['type'];

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

	function createNewCustomer($app,$first_name,$last_name,$user){
		$user = $this->add('xepan\base\Model_User')->load($user->id);
		$email_info = $this->add('xepan\base\Model_Contact_Email');
		$email_info->addCondition('value',$user['username']);
		$email_info->tryLoadAny();

		if($email_info->loaded()){
			$contact = $this->add('xepan\base\Model_Contact')->load($email_info['contact_id']);

			if($contact['type'] == 'Contact'){

				if(!$this->add('xepan\commerce\Model_Customer')->tryLoad($contact->id)->loaded()){
					$this->app->db->dsql()->table('customer')
						->set('contact_id',$contact->id)
						->insert();
				}

				$contact['first_name'] = $first_name;
				$contact['last_name'] = $last_name;
				$contact['type'] = 'Customer';
				$contact['user_id'] = $user->id;
				$contact->save();
				
			}
			
		}else{
			$customer=$this->add('xepan\commerce\Model_Customer');
			$customer['first_name']=$first_name;
			$customer['last_name']=$last_name;
			$customer['user_id']=$user->id;
			$customer->save();
			
			$email = $this->add('xepan\base\Model_Contact_Email');
			$email['contact_id'] = $customer->id;
			$email['head'] = 'Official';
			$email['value'] = $user['username'];
			$email->save();
		}
	}

}
 
    
