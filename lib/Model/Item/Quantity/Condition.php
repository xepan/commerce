<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Condition extends \xepan\base\Model_Table{
 	public $acl =false;
 	public $table = "quantity_condition";
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		//TODO
		// $doc_j->hasOne('xProduction/Phase','department_phase_id');

		$this->hasOne('xepan\commerce\Item_Quantity_Set','quantity_set_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		
		$this->addExpression('type')->set("'QuantityCondition'");

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('customfield_value_id')->fieldQuery('name');
		});

		$this->addExpression('customfield')->set(function($m,$q){
			return $m->refSQL('customfield_value_id')->fieldQuery('customfield_name');
		});

		$this->addExpression('customfield_type')->set(function($m,$q){
			return $m->refSQL('customfield_value_id')->fieldQuery('customfield_type');
		});

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){

		$old = $this->add('xepan\commerce\Model_Item_Quantity_Condition');
		$old->addCondition('quantity_set_id',$this['quantity_set_id']);
		$old->addCondition('customfield_value_id',$this['customfield_value_id']);
		$old->tryLoadAny();

		if($old->loaded() and ($old['id'] != $this['id'])){
			throw $this->exception('Value Alredy Defiend', 'ValidityCheck')->setField('customfield_value_id');
		}


	}

} 
 
	

