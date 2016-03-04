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
		
		//TODO Other Contacts
		$supl_j->addField('tin_no');
		$supl_j->addField('pan_no');

		$this->addCondition('type','Supplier');
		$this->getElement('status')->defaultValue('Active');
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
}

 