<?php 

namespace xepan\commerce;

class Widget_PurchaseOrderStatus extends \xepan\base\Widget{
	function init(){
		parent::init();

		if($this->isChart())			
			$this->view = $this->add('xepan\commerce\View_QSPWidgetHandler',['heading'=>'Purchase Order Status','page'=>'purchaseorder']);	
		else
			$this->grid = $this->add('Grid');
	}

	function recursiveRender(){
		$model = $this->add('xepan\commerce\Model_PurchaseOrder');
		
		if($this->isChart())
			$this->showChart($model);
		else
			$this->showGrid($model);

		return parent::recursiveRender();
	}

	function showChart($model){
		$this->view->setModel($model);
	}

	function showGrid($model){
		$model->addExpression('counts','count(*)');
		$model->_dsql()->group('status');
		// $group_element = $model->dsql()->expr('count([0])',[$model->getElement('status')]);
		// 	$model->dsql()->group($group_element);
		$this->grid->setModel($model,['status','counts']);
		// $this->grid->addColumn('Status');
		// $this->grid->addColumn('counts');
	}
}