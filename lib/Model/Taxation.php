<?php

 namespace xepan\commerce;

 class Model_Taxation extends \xepan\base\Model_Table{
 	public $table="taxation";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		// $this->hasMany('xepan\commerce\Item');
		$this->addField('created_by_id')->defaultValue($this->app->employee->id);
		$this->addField('name')->sortable(true);
		$this->addField('percentage')->sortable(true)->type('Number');
		$this->addField('type')->set('Taxation');
		$this->addField('show_in_qsp')->type('boolean')->defaultValue(true);

		$this->addField('sub_tax')->display(array('form'=>'xepan\base\DropDown'));
		// $this->hasMany('xepan/commerce/QSP_Master','taxation_id');
		$this->addCondition('type','Taxation');

		$this->addHook('afterSave',$this);
		
		$this->hasMany('xepan\commerce\TaxationRuleRow','taxation_id');

		$this->is([
				'name|required',
				'percentage|number|>=0'
				
			]);
	}

	function ledger(){

		$ledger = $this->add('xepan\accounts\Model_Ledger');
		$ledger->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->load("Tax Payable")->get('id'));
		$ledger->addCondition('ledger_type','SalesServiceTaxes');
		$ledger->addCondition('related_id',$this->id);
		$ledger->tryLoadAny();

		if(!$ledger->loaded()){
			$ledger['name'] = $this['name'];
			$ledger['ledger_type'] = 'SalesServiceTaxes';
			$ledger['LedgerDisplayName'] = 'SalesServiceTaxes';
			$ledger->save();
		}else{
			if($ledger['name'] != $this['name']){
				$account['name'] = $this['name'];
				$ledger['updated_at'] = $this->app->now;
				$ledger->save();
			}
		}

		return $ledger;

	}

	function afterSave(){
		if(!isset($this->app->skip_accounts_ledger_creation)){
			$ledger=$this->add('xepan\accounts\Model_Ledger');	
			$ledger->createTaxLedger($this);
		}
	}
}
 
    