<?php

namespace xepan\commerce;

class Widget_DueInvoices extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->report->enableFilterEntity('date_range');
		$this->grid = $this->add('xepan\base\Grid',null,null,['view\dashboard\order']);
	}

	function recursiveRender(){
		$inv = $this->add('xepan\commerce\Model_SalesInvoice');
		$inv->setOrder('due_date','asc');
		$inv->addCondition('status','Due');
		
		if(isset($this->report->start_date))
			$inv->addCondition('created_at','>',$this->report->start_date);
		if(isset($this->report->end_date))
			$inv->addCondition('created_at','<',$this->app->nextDate($this->report->end_date));
		
		$this->grid->setModel($inv);
		$this->grid->template->trySet('heading','Due Invoices');
		$this->grid->addPaginator(5);
		
		return Parent::recursiveRender();
	}
}