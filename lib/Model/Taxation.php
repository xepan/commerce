<?php

 namespace xepan\commerce;

 class Model_Taxation extends \xepan\base\Model_Table{
 	public $table="taxation";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('percentage');
		$this->addField('type')->set('Taxation');

		// $this->hasMany('xepan/commerce/QSP_Master','taxation_id');
		$this->addCondition('type','taxation');

		$this->addHook('afterSave',$this);		
		
	}

	function afterSave(){
		$this->app->hook('tax_update',[$this]);
	}
}
 
    