<?php

/**
* description: FOR CUSTOM FIELD BASED CONSUMPTION CONSTRAINTS
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class Model_Item_Department_ConsumptionConstraint extends \xepan\base\Model_Table{
	public $table = "item_department_consumptionconstraint";

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\commerce\Item_Department_Consumption','item_department_consumption_id');
		$this->hasOne('xepan\commerce\Item_CustomField_Association','item_customfield_asso_id')->display(array('form'=>'xepan\base\Basic'));
		$this->hasOne('xepan\commerce\Item_CustomField_Value','item_customfield_value_id')->display(array('form'=>'xepan\base\Basic'));
		
		$this->addField('item_customfield_id');
		$this->addField('item_customfield_name');
		$this->addField('item_customfield_value_name');

		// $this->addExpression('can_effect_stock')->set($this->ref('item_customfield_asso_id')->getElement('can_effect_stock'));
		$this->addHook('beforeSave',[$this,'updateCustomFieldAndValue']);
		$this->addHook('beforeSave',$this);

		$this->is([
			'item_customfield_asso_id|required'
			]);
	}

	function beforeSave(){

		$old_constraint = $this->add('xepan\commerce\Model_Item_Department_ConsumptionConstraint');
		$old_constraint->addCondition('item_department_consumption_id',$this['item_department_consumption_id']);
		$old_constraint->addCondition('item_customfield_asso_id',$this['item_customfield_asso_id']);
		$old_constraint->addCondition('item_customfield_value_id',$this['item_customfield_value_id']);
		$old_constraint->addCondition('item_customfield_id',$this['item_customfield_id']);
		$old_constraint->addCondition('id','<>',$this['id']);
		$old_constraint->tryLoadAny();		
		if($old_constraint->loaded())
			throw $this->Exception('CONSTRAINTS already added','ValidityCheck')->setField('item_customfield_asso_id');
	}

	function updateCustomFieldAndValue(){
		if($this['item_customfield_asso_id']){
			$asso_model = $this->add('xepan\commerce\Model_Item_CustomField_Association')->load($this['item_customfield_asso_id']);
			$this['item_customfield_id'] = $asso_model['customfield_generic_id'];
			$this['item_customfield_name'] = $asso_model['name'];
		}

		if($this['item_customfield_value_id']){
			$value_model = $this->add('xepan\commerce\Model_Item_CustomField_Value')->load($this['item_customfield_value_id']);
			$this['item_customfield_value_name'] = $value_model['name'];
		}
	}

}
