<?php

namespace xepan\commerce;

class Model_Item_Template extends \xepan\commerce\Model_Item{

	function init(){
		parent::init();
		
		$this->addCondition('is_template',true);
		// $this->addCondition('is_designable',true);
		
	}

	function loadActive(){
		return $this->addCondition('is_publish',true);
	}

	function loadUnactive(){
		return $this->addCondition('is_publish',false);
	}
}