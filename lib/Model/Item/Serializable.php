<?php

namespace xepan\commerce;

class Model_Item_Serializable extends \xepan\commerce\Model_Item{
	function init(){
		parent::init();

		$this->addCondition('is_serializable',true);
	}
}