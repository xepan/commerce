<?php

namespace xepan\commerce;

class View_QSPMissingNo extends \View {

	function init(){
		parent::init();

		$filter = $this->app->stickyGET('filter');
		$looking_for = $this->app->stickyGET('looking_for');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->layout([
					'looking_for'=>'Filter~c1~2',
					'FormButtons~&nbsp;'=>'c6~3'
				]);
		$form->addField('DropDown','looking_for')
				->setValueList(
					[
					'SalesInvoice'=>'Sales Invoice',
					'SalesOrder'=>"Sales Order",
					'PurchaseInvoice'=>'Purchase Invoice',
					'PurchaseOrder'=>'Purchase Order',
					'Quotation'=>"Quotation"
				])->setEmptyText('Please Select');
		
		$form->addSubmit('Filter');
		$view = $this->add('View');

		if($form->isSubmitted()){
			$form->js(null,$view->js()->reload([
					'filter'=>1,
					'looking_for'=>$form['looking_for']
				]))->execute();
		}

		if($filter){
			$miss_nos = $view->add('xepan\commerce\Model_'.$looking_for)
					->getMissingNo();
			$view->add('View')->setElement('h3')->set('Total Missing No. '.count($miss_nos));
			$view->add('View')->set(implode(", ",$miss_nos));
		}

	}
}