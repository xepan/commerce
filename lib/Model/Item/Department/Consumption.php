<?php

/**
* description: Define Item Consumption
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class Model_Item_Department_Consumption extends \xepan\base\Model_Table{
	public $table = "item_department_consumption";

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item_Department_Association','item_department_association_id');
		$this->hasOne('xepan\commerce\Item','composition_item_id')->display(array('form'=>'xepan\commerce\Item'));
		$this->hasOne('xepan\commerce\Item_CustomField_Association','item_customfield_asso_id')->display(array('form'=>'xepan\base\Basic'));
		$this->hasOne('xepan\commerce\Item_CustomField_Value','item_customfield_value_id')->display(array('form'=>'xepan\base\Basic'));
		
		$this->addField('quantity');
		$this->addField('unit');
		$this->addField('custom_fields')->type('text');

		//FOR CUSTOM FIELD BASED CONSUMPTION CONSTRAINTS
		$this->addField('item_customfield_id');
		$this->addField('item_customfield_name');
		$this->addField('item_customfield_value_name');
		
		$this->addHook('beforeSave',[$this,'updateCustomFieldAndValueName']);
	}

	function updateCustomFieldAndValueName(){
		if($this['item_customfield_asso_id']){
			$asso_model = $this->add('xepan\commerce\Model_Item_CustomField_Association')->load($this['item_customfield_asso_id']);
			$this['item_customfield_id'] = $asso_model['customfield_generic_id'];
			$this['item_customfield_name'] = $asso_model['name'];
		}

		if($this['item_customfield_value_id']){
			$value_model = $this->add('xepan\commerce\Model_Item_CustomField_Value')->load($this['item_customfield_value_id']);
			$this['item_customfield_value_name'] = $value_model['name'];
		}

		if(!$this['unit'] and $this['composition_item_id']){
			$item = $this->add('xepan\commerce\Model_Item')->load($this['composition_item_id']);
			$this['unit'] = $item['qty_unit'];
		}

	}

	function isConstraints(){
		if(!$this->loaded())
			throw new \Exception("Error Processing Request", 1);
			
		return $this['item_customfield_id']?true:false;
	}

}
