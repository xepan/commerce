<?php

 namespace xepan\commerce;

 class Model_Taxation extends \xepan\base\Model_Table{
 	public $table="taxation";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		// $this->hasMany('xepan\commerce\Item');
		$this->addField('name')->sortable(true);
		$this->addField('percentage')->sortable(true);
		$this->addField('type')->set('Taxation');

		// $this->hasMany('xepan/commerce/QSP_Master','taxation_id');
		$this->addCondition('type','Taxation');

		$this->addHook('afterSave',$this);		
		
		$this->hasMany('xepan\commerce\Item_Taxation_Association','taxation_id');
	}

	function ledger(){

		$ledger = $this->add('xepan\accounts\Model_Ledger');
		$ledger->addCondition('group_id',$this->add('xepan\accounts\Model_Group')->loadDutiesAndTaxes()->get('id'));
		$ledger->addCondition('ledger_type',$this['name']);
		$ledger->addCondition('related_id',$this->id);
		$ledger->tryLoadAny();

		if(!$ledger->loaded()){
			$ledger['name'] = $this['name'];
			$ledger['LedgerDisplayName'] = $this['name'];
			$ledger->save();
		}else{
			$ledger['name'] = $this['name'];
			$ledger['updated_at'] = $this->app->now;
			$ledger->save();
		}

		return $ledger;

	}

	function afterSave(){
		$ledger=$this->add('xepan\accounts\Model_Ledger');	
		$ledger->createTaxLedger($this);
	}
}
 
    