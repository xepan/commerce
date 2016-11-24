<?php

namespace xepan\commerce;

class page_reports_supplier extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'Supplier Report';

	function init(){
		parent::init();
		$status_array = [ 'draft_odr_count' => 'Draft : Order',
						  'submitted_odr_count'  => 'Submitted : Order',
						  'approved_odr_count'  => 'Approved : Order',
						  'inprogress_odr_count'  => 'InProgress : Order',
						  'canceled_odr_count'  => 'Canceled : Order',
						  'rejected_odr_count'  => 'Rejected : Order',
						  'partialcomplete_odr_count'  => 'PartialComplete : Order',
						  'completed_odr_count'  => 'Completed : Order',						  
						  'draft_inv_count'  => 'Draft : Invoice',
						  'submitted_inv_count'  => 'Submitted : Invoice',
						  'due_inv_count'  => 'Due : Invoice',
						  'paid_inv_count'  => 'Paid : Invoice',
						  'canceled_inv_count'  => 'Canceled : Invoice'  
					    ];

		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$contact = $this->app->stickyGET('contact_id');
		$from_amount = $this->app->stickyGET('from_amount');
		$to_amount = $this->app->stickyGET('to_amount');
		$qsp_status = $this->app->stickyGET('status');
		$order_by = $this->app->stickyGET('order');

		$toggle_button = $this->add('Button',null,'toggle')->set('Show/Hide form')->addClass('btn btn-primary btn-sm');
		$form = $this->add('xepan\commerce\Reports_FilterForm',['extra_field'=>true,'status_array'=>$status_array],'filterform');
		$this->js(true,$form->js()->hide());
		$toggle_button->js('click',$form->js()->toggle());
		
		$supplier_m = $this->add('xepan\commerce\Model_Reports_Supplier',['from_date'=>$from_date, 'to_date'=>$this->app->nextDate($to_date), 'supplier'=>$contact, 'from_amount'=>$from_amount, 'to_amount'=>$to_amount]);

		if($qsp_status && $order_by)
			$supplier_m->setOrder($qsp_status,$order_by);

		$grid = $this->add('xepan\hr\Grid',null,'view',['reports\supplier']);
		$grid->setModel($supplier_m,['name','id','draft_odr_count','submitted_odr_count','approved_odr_count','inprogress_odr_count','redesign_odr_count','canceled_odr_count','rejected_odr_count','partialcomplete_odr_count','completed_odr_count','draft_inv_count','submitted_inv_count','due_inv_count','canceled_inv_count','paid_inv_count']);	
		$grid->addPaginator(30);
		$grid->addQuickSearch(['name']);

		$grid->js('click')->_selector(".col-md-3")->univ()->frameURL('Details',[[$this->api->url(),'page'=>$this->js()->_selectorThis()->closest('[data-url]')->data('url')],'supplier_id'=>$this->js()->_selectorThis()->closest('[data-supplier]')->data('supplier'),'status'=>$this->js()->_selectorThis()->closest('[data-status]')->data('status')]);

		if($form->isSubmitted()){
			$form->validateFields()
				 ->reloadView($grid);
		}				

	}

	function defaultTemplate(){
		return ['reports\pagetemplate'];
	}
}