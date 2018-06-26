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
	public $contact_type = "Customer";

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
		
		$cust_j->addField('billing_name');
		$cust_j->addField('billing_address')->type('text');
		$cust_j->addField('billing_city');
		$cust_j->addField('billing_pincode');

		$cust_j->addField('same_as_billing_address')->type('boolean')->defaultValue(true);
				
		$cust_j->addField('shipping_name');
		$cust_j->addField('shipping_address')->type('text');
		$cust_j->addField('shipping_city');
		$cust_j->addField('shipping_pincode');
		$cust_j->addField('tin_no');
		$cust_j->addField('pan_no');
		$cust_j->addField('is_designer')->type('boolean')->defaultValue(false);

		// gst related fields
		$cust_j->addField('customer_type')->setValueList(['business'=>'Business','individual'=>'Individual'])->defaultValue('business')->display(['form'=>'xepan\base\DropDown']);
		$cust_j->addField('gstin');

		$this->hasMany('xepan/commerce/Model_QSP_Master',null,null,'QSPMaster');
		$this->hasMany('xepan/commerce/Model_Designer_Image_Category','contact_id');
		$this->hasMany('xepan/commerce/Model_Credit','customer_id');
		$this->hasMany('xepan/commerce/FreelancerCatAndCustomerAssociation','customer_id','FreelancerCategoryAssociation');
		
		$this->addExpression('customer_currency_icon',function($m,$q){
			return $m->refSQL('currency_id')->fieldQuery('icon');
		});

		$this->addExpression('organization_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization_name]) OR trim([organization_name])="" ,[contact_name],[organization_name])',
						[
							'contact_name'=>$m->getElement('name'),
							'organization_name'=>$m->getElement('organization')
						]
					);
		});

		//TODO Extra Organization Specific Fields other Contacts
		$this->getElement('status')->defaultValue('Active');
		$this->addCondition('type','Customer');
		$this->addHook('afterSave',[$this,'defaultAfterSave']);
		$this->addHook('beforeDelete',[$this,'checkQSPExistance']);	
		$this->addHook('beforeSave',[$this,'updateSearchString']);
		
	}

	function checkQSPExistance($m){
		$customer_qsp_count = $m->ref('QSPMaster')->count()->getOne();
		
		if($customer_qsp_count){
			throw new \Exception("First delete the invoice/order/.. of this customer");
			
		}	
	}

	function defaultAfterSave(){
		$this->app->hook('customer_update',[$this]);
	}

	//activate Customer
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Customer : '".$this['name']."' now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	//deactivate Customer
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Customer : '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('activate','InActive',$this);
		return $this->save();
	}

	function ledger(){
		$account = $this->add('xepan\accounts\Model_Ledger')
				->addCondition('contact_id',$this->id)
				->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->load("Sundry Debtor")->get('id'));
		$account->tryLoadAny();
		if(!$account->loaded()){
			$account['name'] = $this['unique_name'];
			$account['LedgerDisplayName'] = $this['unique_name'];
			$account['ledger_type'] = 'Customer';
			$account->save();
		}else{
			if($account['name'] != $this['unique_name']){
				$account['name'] = $this['unique_name'];
				$account['updated_at'] = $this->app->now;
				$account->save();
			}
		}

		return $account;

	}

	function updateAddress($address_detail=[]){
		if(!$this->loaded())
			throw new \Exception("customer not found");
			
		if(!count($address_detail))
			throw new \Exception("billing or shipping address not found");
		
		$this['billing_name'] = $address_detail['billing_name'];
		$this['billing_address'] = $address_detail['billing_address'];
		$this['billing_city'] = $address_detail['billing_city'];
		$this['billing_state_id'] = $address_detail['billing_state_id'];
		$this['billing_country_id'] = $address_detail['billing_country_id'];
		$this['billing_pincode'] = $address_detail['billing_pincode'];
		
		$this['shipping_name'] = $address_detail['shipping_name'];
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
		$search_string .=" ". $this['billing_name'];
		$search_string .=" ". $this['billing_address'];
		$search_string .=" ". $this['billing_city'];
		$search_string .=" ". $this['billing_state_id'];
		$search_string .=" ". $this['billing_country_id'];
		$search_string .=" ". $this['billing_pincode'];
		$search_string .=" ". $this['shipping_name'];
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

	function createNewCustomer($app,$contact_detail=[],$user){

		// $user = $this->add('xepan\base\Model_User')->load($user->id);
		// $email_info = $this->add('xepan\base\Model_Contact_Email');
		// $email_info->addCondition('value',$user['username']);
		// $email_info->tryLoadAny();

		$contact = $this->add('xepan\base\Model_Contact')->tryLoadBy('user_id',$user->id);
		if($contact->loaded()){

			if($contact['type'] == 'Contact'){

				if(!$this->add('xepan\commerce\Model_Customer')->tryLoad($contact->id)->loaded()){
					$this->app->db->dsql()->table('customer')
						->set('contact_id',$contact->id)
						->insert();
				}

				$contact['first_name'] = $contact_detail['first_name'];
				$contact['last_name'] = $contact_detail['last_name'];
				$contact['type'] = 'Customer';
				$contact['user_id'] = $user->id;

				if(isset($contact_detail['country']))
					$contact['country_id'] = $contact_detail['country'];
				if(isset($contact_detail['state']))
					$contact['state_id'] = $contact_detail['state'];
				if(isset($contact_detail['city']))
					$contact['city'] = $contact_detail['city'];
				if(isset($contact_detail['address']))
					$contact['address'] = $contact_detail['address'];
				if(isset($contact_detail['pin_code']))
					$contact['pin_code'] = $contact_detail['pin_code'];

				$contact->save();
			}
			
		}else{
			$customer=$this->add('xepan\commerce\Model_Customer');
			$customer['first_name'] = $contact_detail['first_name'];
			$customer['last_name'] = $contact_detail['last_name'];
			$customer['user_id'] = $user->id;

			if(isset($contact_detail['country']))
				$customer['country_id'] = $contact_detail['country'];
			if(isset($contact_detail['state']))
				$customer['state_id'] = $contact_detail['state'];
			if(isset($contact_detail['city']))
				$customer['city'] = $contact_detail['city'];
			if(isset($contact_detail['address']))
				$customer['address'] = $contact_detail['address'];
			if(isset($contact_detail['pin_code']))
				$customer['pin_code'] = $contact_detail['pin_code'];

			$customer->save();
			$contact = $customer;
		}

		if(filter_var($user['username'], FILTER_VALIDATE_EMAIL)){
			$email = $this->add('xepan\base\Model_Contact_Email');
			$email->addCondition('contact_id',$contact->id);
			$email->addCondition('value',$user['username']);
			$email->tryLoadAny();
			
			$email['head'] = 'Official';
			$email->save();
		}
		
		if(isset($contact_detail['mobile_no']) && $contact_detail['mobile_no']){
			$phone = $this->add('xepan\base\Model_Contact_Phone');
			$phone->addCondition('contact_id',$contact->id);
			$phone->addCondition('value',$contact_detail['mobile_no']);
			$phone->tryLoadAny();

			$phone['head'] = 'Official';
			$phone->save();
		}

	}

	function getAssociatedCategories(){
		$associated_categories = $this->add('xepan\commerce\Model_FreelancerCatAndCustomerAssociation');
		$associated_categories->addCondition('customer_id',$this->id);
		$array = $associated_categories->_dsql()->del('fields')->field('freelancer_category_id')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array)),false);
	}



	function addCustomerFromCSV($data){
		// multi record loop
			foreach ($data as $key => $record) {

				try{
					$this->api->db->beginTransaction();

					$email_array = ['personal'=>[],'official'=>[]];
					$contact_array = ['personal'=>[],'official'=>[]];
					$category = [];

					$customer = $this->add('xepan\commerce\Model_Customer');
					foreach ($record as $field => $value) {
						$field = strtolower(trim($field));
						$value = trim($value);

						// category selection
						if($field == "category" && $value){
							$category = explode(",",$value);
							continue;
						}

						// official contact
						if(strstr($field, 'official_contact') && $value){
							$contact_array['official'][] = $value;
							continue;
						}
						// official email
						if(strstr($field, 'official_email') && $value){
							$email_array['official'][] = $value;
							continue;
						}

						// Personal contact
						if(strstr($field, 'personal_contact') && $value){
							$contact_array['personal'][] = $value;
							continue;
						}
						// official email
						if(strstr($field, 'personal_email') && $value){
							$email_array['personal'][] = $value;
							continue;
						}

						if($field == "country"){
							$country = $this->add('xepan\base\Model_Country')->addCondition('name','like',$value)->tryLoadAny();
							if(!$country->loaded())
								continue;
							$value = $country->id;
						}

						if($field == "state"){
							$state = $this->add('xepan\base\Model_State')->addCondition('name','like',$value)->tryLoadAny();
							if(!$state->loaded())
								continue;
							$value = $state->id;
						}

						$customer[$field] = $value;
					}

					// try{
						$customer->save();

					// insert email official ids			
					foreach ($email_array['official'] as $key => $email) {
						$email_model = $this->add('xepan\base\Model_Contact_Email');
						$email_model->addCondition('value',$email);
						$email_model->tryLoadAny();
						
						if(!$email_model->loaded()){
							$email_model['contact_id'] = $customer->id;
							$email_model['head'] = "Official";				
							$email_model['value'] = $email;
							$email_model->save();
						}
					}
					
					foreach ($email_array['personal'] as $key => $email) {
						$email_model = $this->add('xepan\base\Model_Contact_Email');
						$email_model['value'] = $email;
						$email_model->addCondition('value',$email);
						$email_model->tryLoadAny();
						
						if(!$email_model->loaded()){
							$email_model['contact_id'] = $customer->id;
							$email_model['head'] = "Personal";				
							$email_model['value'] = $email;
							$email_model->save();
						}
					}

					// insert offical contact numbers
					foreach($contact_array['official'] as $key => $contact){
						$phone = $this->add('xepan\base\Model_Contact_Phone');
						$phone->addCondition('value',$contact);
						$phone->tryLoadAny();

						if(!$phone->loaded()){
							$phone['contact_id'] = $customer->id;
							$phone['head'] = "Official";
							$phone->save();
						}
					}

					// insert offical contact numbers
					foreach($contact_array['personal'] as $key => $contact){
						$phone = $this->add('xepan\base\Model_Contact_Phone');
						$phone->addCondition('value',$contact);
						$phone->tryLoadAny();

						if(!$phone->loaded()){
							$phone['contact_id'] = $customer->id;
							$phone['head'] = "Personal";
							$phone->save();
						}
					}

					$customer->unload();

					$this->api->db->commit();
				}catch(\Exception $e){
					echo $e->getMessage()."<br/>";
					continue;
				// 	// throw $e;
					// $this->api->db->rollback();
				}
			}
	}

	function getAddress(){
		if(!$this->loaded()) throw new \Exception("customer not found");
		
		return [
				'address'=> $this['address'],
				'city'=> $this['city'],
				'state_id'=> $this['state_id'],
				'state'=> $this['state'],
				'country_id'=> $this['country_id'],
				'country'=> $this['country'],
				'pin_code'=> $this['pin_code'],
				
				'billing_address' =>trim($this['billing_address'])?:$this['address'],
				'billing_city'=>trim($this['billing_city'])?:$this['city'],
				'billing_state_id'=> trim($this['billing_state_id'])?:$this['state_id'],
				'billing_state'=> trim($this['billing_state'])?:$this['state'],
				'billing_country_id'=> trim($this['billing_country_id'])?:$this['country_id'],
				'billing_country'=> trim($this['billing_country'])?:$this['country'],
				'billing_pincode'=> trim($this['billing_pincode'])?:$this['pin_code'],

				'shipping_address' => trim($this['shipping_address'])?:$this['address'],
				'shipping_city'=> trim($this['shipping_city'])?:$this['city'],
				'shipping_state_id'=> trim($this['shipping_state_id'])?:$this['state_id'],
				'shipping_state'=> trim($this['shipping_state'])?:$this['state'],
				'shipping_country_id'=> trim($this['shipping_country_id'])?:$this['country_id'],
				'shipping_country'=> trim($this['shipping_country'])?:$this['country'],
				'shipping_pincode'=> trim($this['shipping_pincode'])?:$this['pin_code']
		];
	}

}
 
    
