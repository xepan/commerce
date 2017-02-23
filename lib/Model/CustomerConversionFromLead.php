<?php

/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class Model_CustomerConversionFromLead extends \xepan\base\Model_Contact{
	function init(){
		parent::init();
		$this->addCondition('type','<>','Customer');
	}
}
 
    
