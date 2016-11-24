<?php 

namespace xepan\commerce;

class Widget_SaleOrderStatus extends \xepan\base\Widget{
	function init(){
		parent::init();
			
		$this->view = $this->add('xepan\commerce\View_QSPWidgetHandler',['heading'=>'Sales Order Status','page'=>'salesorder']);	
	}

	function recursiveRender(){
		$model = $this->add('xepan\commerce\Model_SalesOrder');
		$this->view->setModel($model);

		return parent::recursiveRender();
	}
}