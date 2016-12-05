<?php

namespace xepan\commerce;

class page_store_reports_shortitem extends \xepan\commerce\page_store_reports_storereportsidebar{
	public $title = "Short Items";

	function init(){
		parent::init();

		$grid= $this->add('xepan\hr\Grid');
		$model = $this->add('xepan\commerce\Model_Item_Stock');
		$model->addCondition('minimum_stock_limit','>','net_stock');
		$grid->setModel($model,['name','minimum_stock_limit','net_stock']);		
	}
}