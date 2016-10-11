<?php

namespace xepan\commerce;

/**
* 
*/
class page_report extends \xepan\base\Page
{
	public $title="Commerce Report";
	function init()
	{
		parent::init();

		// $x=$this->app->side_menu;
		// $x->addItem(['Sales Order','icon'=>' fa fa-envelope'],'xepan_commerce_report_salesorder')->setAttr(['title'=>'Sales Order Report']);
		$f=$this->add('Form');
		$type = $f->addField('DropDown','report_type')
		                            ->setValueList(
									[
										'QSP_Master'=>'All',
										'SalesOrder'=>'SalesOrder',
										'SalesInvoice'=>'SalesInvoice',
										'PurchaseOrder'=>'PurchaseOrder',
										'PurchaseInvoice'=>'PurchaseInvoice'
									])->setEmptyText('Please Select');

		$f->addField('DatePicker','from_date');
		$f->addField('DatePicker','to_date');
		$f->addField('line','from_amount');
		$f->addField('line','to_amount');
		$f->addField('autocomplete/Basic','contact')->setModel('xepan\base\Contact');

		// $type->js('change',$f->js()->reload(['doc_type'=>$f['report_type']]));

		$doc_type = $this->app->stickyGET('doc_type');

		$view = $this->add('View');
		$f->addSubmit('Get Report');

		if($doc_type){
			$grid = $view->add('xepan\base\Grid');
			
			$m=$this->add('xepan\commerce\Model_'.$doc_type);
			
			$this->app->stickyGET('contact');
			$this->app->stickyGET('from_date');			
			$this->app->stickyGET('to_date');			
			$this->app->stickyGET('from_amount');			
			$this->app->stickyGET('to_amount');
				
			if($_GET['from_date'])
				$m->addCondition('created_at','>=',$_GET['from_date']);
			if($_GET['to_date'])
				$m->addCondition('created_at','<=',$this->app->nextDate($_GET['to_date']));						
			if($_GET['contact_id'])
				$m->addCondition('contact_id',$_GET['contact']);
			if($_GET['from_amount'])
				$m->addCondition('net_amount','>=',$_GET['from_amount']);
			if($_GET['to_amount'])
				$m->addCondition('net_amount','<=',$_GET['to_amount']);


			$grid->setModel($m,['contact','created_at','total_amount','gross_amount','tax_amount','net_amount','due_date']);		
			$grid->addQuickSearch(['contact']);
		}							
			
		
		if($f->isSubmitted()){
			// $m=$this->add('xepan\commerce\Model_'.$f['report_type']);
			$array = [
						'doc_type'=>$f['report_type'],
						'to_date'=>$f['to_date']?:0,
						'from_date'=>$f['from_date']?:0,
						'from_amount'=>$f['from_amount'],
						'to_amount'=>$f['to_amount'],
						'contact'=>$f['contact']
					];
			$view->js()->reload($array)->execute();
		}

	}
}