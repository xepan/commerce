<?php

namespace xepan\commerce;

class page_test extends \Page{
	
	function init(){
		parent::init();

		$ord = $this->add('xepan\commerce\Model_SalesOrder')
			->addCondition('contact_id',656);
			$crud_ord = $this->add('xepan\hr\CRUD',null,null,['view/customer/order/grid']);
			$crud_ord->setModel($ord);
			$crud_ord->grid->addQuickSearch(['orders']);

		}
}