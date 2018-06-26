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
					'Active'=>['view','edit','delete','deactivate','communication'],
					'InActive'=>['view','edit','delete','activate','communication']
					];
	public $contact_type = "Supplier";
	
	function init(){
		parent::init();

		$this->getElement('created_by_id')->defaultValue($this->app->employee->id);

		$supl_j=$this->join('supplier.contact_id');
		$supl_j->hasOne('xepan\accounts\Currency','currency_id');
		//TODO Other Contacts
		$supl_j->addField('tin_no');
		$supl_j->addField('pan_no');

		$supl_j->addField('bank_name');
		$supl_j->addField('bank_ifsc_code');
		$supl_j->addField('account_no');
		$supl_j->addField('account_type')->enum(['current','saving']);

		$this->hasMany('xepan/commerce/Model_QSP_Master',null,null,'QSPMaster');
		
		$this->addCondition('type','Supplier');
		$this->getElement('status')->defaultValue('Active');
		$this->addHook('afterSave',$this);
		$this->addHook('beforeDelete',$this);
		$this->addHook('beforeSave',[$this,'updateSearchString']);
	}
	
	function afterSave(){
		$this->app->hook('supplier_update',[$this]);
	}

	function beforeDelete($m){
	$customer_qsp_count = $m->ref('QSPMaster')->count()->getOne();
	
	if($customer_qsp_count){
			throw new \Exception("First delete the invoice/order/.. of this supplier");	
		}	
	}

	//activate Supplier
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Supplier : '".$this['name']."' now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_supplierdetail&contact_id=".$this->id."")
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	//deactivate Supplier
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Supplier : '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_supplierdetail&contact_id=".$this->id."")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}
	
	function ledger(){
		$account = $this->add('xepan\accounts\Model_Ledger')
				->addCondition('contact_id',$this->id)
				->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->load("Sundry Creditor")->get('id'));
		$account->tryLoadAny();
		if(!$account->loaded()){
			$account['name'] = $this['unique_name'];
			$account['LedgerDisplayName'] = $this['unique_name'];
			$account['ledger_type'] = 'Supplier';
			$account->save();
		}else{
			$account['name'] = $this['unique_name'];
			$account['updated_at'] = $this->app->now;
			$account->save();
		}

		return $account;

	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['name'];
		$search_string .=" ". str_replace("<br/>", " ", $this['contacts_str']);
		$search_string .=" ". str_replace("<br/>", " ", $this['emails_str']);
		$search_string .=" ". $this['address'];
		$search_string .=" ". $this['city'];
		$search_string .=" ". $this['type'];
		$search_string .=" ". $this['state'];
		$search_string .=" ". $this['country'];
		$search_string .=" ". $this['pin_code'];
		$search_string .=" ". $this['organization'];
		$search_string .=" ". $this['pan_no'];
		$search_string .=" ". $this['tin_no'];

		if($this->loaded()){
			$qsp_master = $this->ref('QSPMaster');
			foreach ($qsp_master as $all_qsp_detail) 
			{
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

 