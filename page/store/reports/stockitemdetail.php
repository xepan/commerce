<?php

namespace xepan\commerce;

class page_store_reports_stockitemdetail extends \xepan\base\Page{
	function init(){
		parent::init();

		$type = $this->app->stickyGET('type');
		$item_id = $this->app->stickyGET('item_id');
		
		$grid= $this->add('xepan\hr\Grid');
		$model = $this->add('xepan\commerce\Model_Item_Stock');
		$model->addCondition('id',$item_id);
		$model->addCondition($type,'>',0);
		$grid->setModel($model,['name',$type]);		
	}
}