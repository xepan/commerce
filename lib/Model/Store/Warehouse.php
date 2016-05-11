<?php
namespace xepan\commerce;
class Model_Store_Warehouse extends \xepan\base\Model_Contact{
	// public $table="store_warehouse";
	public $acl=false;

	function init(){
		parent::init();

		$this->addCondition('type','Warehouse');

		$this->hasMany('xepan\commerce\Store_Transaction','from_warehouse_id',null,'FromTransactions');
		$this->hasMany('xepan\commerce\Store_Transaction','to_warehouse_id',null,'ToTransactions');
		
		$this->addHook('beforeSave',[$this,'updateSearchString']);

	}


	function newTransaction($related_document_id,$jobcard_id=null,$related_doc_contact_id,$related_document_type){
		
		$m = $this->add('xepan\commerce\Model_Store_Transaction');
		$m['document_type'] = $related_document_type;
		$m['from_warehouse_id'] = $related_doc_contact_id;
		$m['to_warehouse_id'] = $this->id;
		$m['related_document_id']=$related_document_id	;	
		$m['jobcard_id']=$jobcard_id;	
		$m['status']='ToReceived';	
		$m->save();
		return $m;
	}

	function updateSearchString($m){

		$search_string = ' ';
		$search_string .=" ". $this['name'];
		$search_string .=" ". $this['address'];
		$search_string .=" ". $this['city'];
		$search_string .=" ". $this['state'];
		$search_string .=" ". $this['pin_code'];
		$search_string .=" ". $this['type'];

		$this['search_string'] = $search_string;
	}

}