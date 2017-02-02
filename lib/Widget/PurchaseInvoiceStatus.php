<?php 

namespace xepan\commerce;

class Widget_PurchaseInvoiceStatus extends \xepan\base\Widget{
	function init(){
		parent::init();
		
		if(!$this->isChart())
			$this->destroy();
		
		$this->view = $this->add('xepan\commerce\View_QSPWidgetHandler',['heading'=>'Purchase Invoice Status','page'=>'purchaseinvoice']);	
	}

	function recursiveRender(){
		$model = $this->add('xepan\commerce\Model_PurchaseInvoice');
		$this->view->setModel($model);

		return parent::recursiveRender();
	}
}