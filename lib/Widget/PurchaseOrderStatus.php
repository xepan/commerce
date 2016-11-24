<?php 

namespace xepan\commerce;

class Widget_PurchaseOrderStatus extends \xepan\base\Widget{
	function init(){
		parent::init();
			
		$this->view = $this->add('xepan\commerce\View_QSPWidgetHandler',['heading'=>'Purchase Order Status','page'=>'purchaseorder']);	
	}

	function recursiveRender(){
		$model = $this->add('xepan\commerce\Model_PurchaseOrder');
		$this->view->setModel($model);

		return parent::recursiveRender();
	}
}