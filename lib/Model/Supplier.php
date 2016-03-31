<?php


/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

 namespace xepan\commerce;
 
 class Model_Supplier extends \xepan\base\Model_Contact{

 	public $status = ['Active','InActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'InActive'=>['view','edit','delete','activate']
					];

	function init(){
		parent::init();

		$supl_j=$this->join('supplier.contact_id');
		$cust_j=$this->join('customer.contact_id');
		$cust_j->hasOne('xepan\accounts\Currency','currency_id');
		//TODO Other Contacts
		$supl_j->addField('tin_no');
		$supl_j->addField('pan_no');

		$this->addCondition('type','Supplier');
		$this->getElement('status')->defaultValue('Active');
		$this->addHook('afterSave',$this);
	}
	
	function afterSave(){
		$this->app->hook('supplier_update',[$this]);
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
				->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadSundryCreditor()->fieldQuery('id'));
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

 