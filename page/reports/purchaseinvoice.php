<?php

namespace xepan\commerce;

class page_reports_purchaseinvoice extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'Purchase Invoice Report';

	function init(){
		parent::init();

		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$contact = $this->app->stickyGET('contact_id');
		$from_amount = $this->app->stickyGET('from_amount');
		$to_amount = $this->app->stickyGET('to_amount');

		$toggle_button = $this->add('Button',null,'toggle')->set('Show/Hide form')->addClass('btn btn-primary btn-sm');
		$form = $this->add('xepan\commerce\Reports_FilterForm',null,'filterform');
		$this->js(true,$form->js()->hide());
		$toggle_button->js('click',$form->js()->toggle());
		
		$purchase_invoice_m = $this->add('xepan\commerce\Model_PurchaseInvoice');
		$purchase_invoice_m->setOrder('created_at','desc');
		
		if($from_date){
			$purchase_invoice_m->addCondition('created_at','>',$from_date);	
			$purchase_invoice_m->addCondition('created_at','<',$this->app->nextDate($to_date));	
		}
		if($contact){			
			$purchase_invoice_m->addCondition('contact_id',$contact);	
		}
		if($from_amount){
			$purchase_invoice_m->addCondition('net_amount','>=',$from_amount);	
		}
		if($to_amount){
			$purchase_invoice_m->addCondition('net_amount','<=',$to_amount);	
		}	

		$grid = $this->add('xepan\hr\Grid',null,'view',['reports\qspgrid']);
		$grid->setModel($purchase_invoice_m,['contact','created_at','total_amount','gross_amount','tax_amount','net_amount','due_date']);	
		$grid->addPaginator(50);
		$grid->addQuickSearch(['contact','total_amount','gross_amount','tax_amount','net_amount']);

		$grid->js('click')->_selector('.commerce-qsp-report')->univ()->frameURL('Purchase Invoice Details',[$this->api->url('xepan_commerce_purchaseinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);

		if($form->isSubmitted()){
			$form->validateFields()
				 ->reloadView($grid);
		}		
	}

	function defaultTemplate(){
		return ['reports\pagetemplate'];
	}
}