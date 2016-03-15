<?php

namespace xepan\commerce;

class Model_Item_Purchasable extends \xepan\commerce\Model_Item{
	function init(){
		parent::init();

		$this->addCondition('is_purchasable',true);
	}
}