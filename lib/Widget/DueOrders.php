<?php

namespace xepan\commerce;

class Widget_DueOrders extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->report->enableFilterEntity('date_range');
		$this->grid = $this->add('xepan\base\Grid',null,null,['view\dashboard\order']);
	}

	function recursiveRender(){
		$so = $this->add('xepan\commerce\Model_SalesOrder');
		$so->setOrder('created_at','desc');
		
		if(isset($this->report->start_date))
			$so->addCondition('created_at','>',$this->report->start_date);
		if(isset($this->report->end_date))
			$so->addCondition('created_at','<',$this->app->nextDate($this->report->end_date));			
		
		$this->grid->setModel($so);
		$this->grid->template->trySet('heading','Due Orders');
		$this->grid->addPaginator('5');
				
		return Parent::recursiveRender();
	}
}