<?php

namespace xepan\commerce;

class Model_Item_Productionable extends \xepan\commerce\Model_Item{
	function init(){
		parent::init();

		$this->addCondition('is_productionable',true);
	}
}