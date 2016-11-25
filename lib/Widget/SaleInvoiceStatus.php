<?php 

namespace xepan\commerce;

class Widget_SaleInvoiceStatus extends \xepan\base\Widget{
	function init(){
		parent::init();
			
		$this->view = $this->add('xepan\commerce\View_QSPWidgetHandler',['heading'=>'Sales Invoice Status','page'=>'salesinvoice']);
	}

	function recursiveRender(){
		$model = $this->add('xepan\commerce\Model_SalesInvoice');
		$this->view->setModel($model);

		return parent::recursiveRender();
	}
}