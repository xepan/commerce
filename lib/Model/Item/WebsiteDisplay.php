<?php

namespace xepan\commerce;

class Model_Item_WebsiteDisplay extends \xepan\commerce\Model_Item{
	function init(){
		parent::init();

		$this->addCondition('website_display',true);
		$this->addCondition(
							$this->dsql()->orExpr()
  								->where('to_customer_id',null)
  								->where('to_customer_id',0)
  							);
		$this->addCondition('is_template',false);
	}
}