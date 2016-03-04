<?php
namespace xepan\commerce;
class Model_Currency extends \xepan\hr\Model_Document{
	// public $table="currency";
	public $status=['Active','InActive'];
	
	public $actions = [
		'Active'=>['view','edit','delete','deactivate'],
		'InActive' => ['view','edit','delete','activate']
	];
	
	function init(){
		parent::init();
		$currency_j = $this->join('currency.document_id');
		$currency_j->addField('icon');
		$currency_j->addField('name');
		$currency_j->addField('value');
		$currency_j->hasMany('xepan\commerce\Customer','currency_id','Customers');
	
		$this->getElement('status')->enum($this->status)->defaultValue('InActive');
		$this->addCondition('type','Currency');
	
	}

	function activate(){
		$this['status']='Active';
		$this->saveAndUnload();
	}

	function deactivate(){
		$this['status']='InActive';
		$this->saveAndUnload();
	}
}