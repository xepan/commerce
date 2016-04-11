<?php

 namespace xepan\commerce;

 class Model_Taxation extends \xepan\base\Model_Table{
 	public $table="taxation";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		// $this->hasMany('xepan\commerce\Item');
		$this->addField('name');
		$this->addField('percentage');
		$this->addField('type')->set('Taxation');

		// $this->hasMany('xepan/commerce/QSP_Master','taxation_id');
		$this->addCondition('type','taxation');

		$this->addHook('afterSave',$this);		
		
		$this->hasMany('xepan\commerce\Item_Taxation_Association','taxation_id');
	}

	function afterSave(){
		$ledger=$this->add('xepan\accounts\Model_Ledger');	
		$ledger->createTaxLedger($this);
	}
}
 
    