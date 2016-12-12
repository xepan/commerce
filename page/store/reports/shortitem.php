<?php

namespace xepan\commerce;

class page_store_reports_shortitem extends \xepan\commerce\page_store_reports_storereportsidebar{
	public $title = "Short Items";

	function init(){
		parent::init();

		$model = $this->add('xepan\commerce\Model_Item_Stock');
		$model->addExpression('required')->set(function($m,$q){
			return $q->expr("[0]-[1]",
							[
								$m->getElement('minimum_stock_limit'),
								$m->getElement('net_stock')
							]);
		});	

		$model->addCondition('minimum_stock_limit','<>',null);
		$model->addCondition($model->dsql()->expr("[0]<[1]",[
								$model->getElement('net_stock'),
								$model->getElement('minimum_stock_limit')
							]));

		$grid= $this->add('xepan\hr\Grid');
		$grid->setModel($model,['name','minimum_stock_limit','net_stock','required']);		
	}
}