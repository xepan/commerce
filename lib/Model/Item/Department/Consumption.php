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
		
		$this->addField('quantity');
		$this->addField('unit');
		$this->addField('custom_fields')->type('text');

		$this->hasMany('xepan\commerce\Item_Department_ConsumptionConstraint','item_department_consumption_id');

		$this->addHook('beforeSave',$this);

		$this->is([
				'composition_item_id|required',
				'quantity|to_trim|required',
				'unit|to_trim|required'
			]);
	}

	function beforeSave(){
		
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
