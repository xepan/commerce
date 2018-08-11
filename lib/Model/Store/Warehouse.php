<?php
namespace xepan\commerce;
class Model_Store_Warehouse extends \xepan\base\Model_Contact{
	// public $table="store_warehouse";
	public $acl=false;
	public $type = "Warehouse";
	public $use_contact = false;
	public $contact_type = "StoreWarehouse";
	public $title_field = "warehouse";
	function init(){
		parent::init();

		if(!$this->use_contact)
			$this->addCondition('type',$this->type);
		
		$this->getElement('first_name')->caption('Name');

		$this->hasMany('xepan\commerce\Store_Transaction','from_warehouse_id',null,'FromTransactions');
		$this->hasMany('xepan\commerce\Store_Transaction','to_warehouse_id',null,'ToTransactions');
		
		$this->addHook('beforeSave',[$this,'updateSearchString']);

		$this->addExpression('warehouse')->set(function($m,$q){
			return $q->expr('CONCAT(IFNULL([0],"")," :: ",IFNULL([1],""))',[$this->getElement('first_name'),$this->getElement('branch')]);
		});

	}

	function loadDefault(){
		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
		$warehouse->tryLoadAny();
	}


	function newTransaction($related_document_id,$jobcard_id=null,$from_warehouse_id,$transaction_type=null,$department_id = null,$to_warehouse_id=null,$narration=null,$subtype=null,$status="ToReceived",$created_at=null){
		$m = $this->add('xepan\commerce\Model_Store_TransactionAbstract');
		$m['type'] = $transaction_type;
		$m['from_warehouse_id'] = $from_warehouse_id;
		$m['to_warehouse_id'] = $to_warehouse_id?:$this->id;
		$m['related_document_id']=$related_document_id	;	
		$m['jobcard_id']=$jobcard_id;
		$m['status']=$status;
		$m['department_id']=$department_id;
		$m['narration']=$narration;
		$m['subtype'] = $subtype;

		if($created_at)
			$m['created_at'] = $created_at;
		else
			$m['created_at'] = $this->app->now;
		
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