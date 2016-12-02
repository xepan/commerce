<?php

namespace xepan\commerce;

class Widget_MonthlyInvoices extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->report->enableFilterEntity('date_range');
		$this->grid = $this->add('xepan\hr\Grid',null,null,['view\dashboard\invoicecount']);
	}

	function recursiveRender(){
		$start_date = $this->report->start_date;
		$end_date = $this->app->nextDate($this->report->end_date);

		$salesinvoice_m = $this->add('xepan\commerce\Model_SalesInvoice');
		$salesinvoice_m->setOrder('created_at','desc');
		$salesinvoice_m->addExpression('monthyear')->set('DATE_FORMAT(created_at,"%M %Y")');
		$salesinvoice_m->addExpression('count','count(*)');
		$salesinvoice_m->_dsql()->group('monthyear');
		
		if($this->report->start_date)
			$salesinvoice_m->addCondition('created_at','>=',$start_date);
		if($this->report->end_date)
			$salesinvoice_m->addCondition('created_at','<=',$end_date);

		$this->grid->setModel($salesinvoice_m);
		$this->grid->addPaginator('5');
		$this->grid->js('click')->_selector('.xepan-invoice-count')->univ()->frameURL('Invoice',[$this->api->url('xepan_commerce_salesinvoice'),'from_date'=>$start_date,'to_date'=>$end_date,'monthyear'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		
		return Parent::recursiveRender();
	}
} 