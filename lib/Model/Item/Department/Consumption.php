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
		$this->hasOne('xepan\commerce\Item','composition_item_id');
		
		$this->addField('quantity');
		$this->addField('unit');
		$this->addField('custom_fields')->type('text');

	}

}
