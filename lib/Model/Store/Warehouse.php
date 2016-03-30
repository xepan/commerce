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
	}


	function newTransaction($related_document_id,$jobcard,$related_doc_contact_id=null,$related_document_type){

		// throw new \Exception($jobcard->id);
		
		$m = $this->add('xepan\commerce\Model_Store_Transaction');
		$m['type'] = $related_document_type;
		$m['from_warehouse_id'] = $related_doc_contact_id;
		$m['to_warehouse_id'] = $this->id;
		$m['related_document_id']=$related_document_id	;	
		$m['jobcard_id']=$jobcard->id;	
		$m['status']='ToReceived';	
		$m->save();
		return $m;
	}

}