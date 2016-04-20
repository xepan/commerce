<?php

/**
* description: Item Department Associations and this table also used for the item 
* composition and consuption means for making this item which item should be consumed
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class Model_Item_Department_Association extends \xepan\base\Model_Table{
	public $table = "item_department_association";

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\hr\Model_Department','department_id')->defaultValue(0);
		$this->addField('can_redefine_qty')->type('boolean')->defaultValue(true);
		$this->addField('can_redefine_item')->type('boolean')->defaultValue(true);

		$this->hasMany('xepan\commerce\Item_Department_Consumption','item_department_association_id');
		
		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){

		$consumption = $this->add('xepan\commerce\Model_Item_Department_Consumption')->addCondition('item_department_association_id',$this->id);
		
		foreach ($consumption as $value) {
			$value->delete();
		}
	}
}
