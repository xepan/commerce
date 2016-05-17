<?php

namespace xepan\commerce;

class page_test extends \xepan\base\Page{
	
	function init(){
		parent::init();

		$ord = $this->add('xepan\commerce\Model_Store_TransactionRow');
		// 	->addCondition('contact_id',656);

		}
}