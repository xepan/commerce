<?php

/**
* description: Item Department Associations
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
		$this->hasOne('xepan\hr\Model_Department','department_id');
		$this->addField('can_redefine_qty')->type('boolean')->defaultValue(true);
		$this->addField('can_redefine_item')->type('boolean')->defaultValue(true);

	}

}
