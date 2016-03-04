<?php

/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class Model_Item_Saleable extends \xepan\commerce\Model_Item{
	function init(){
		parent::init();

		$this->addCondition('');
	}
}

//online sale
//empty
//purchase
//website display
//offline sale
//designable
