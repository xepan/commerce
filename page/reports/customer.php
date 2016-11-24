<?php

namespace xepan\commerce;

class page_reports_customer extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'Customer Report';

	function init(){
		parent::init();
		
		$status_array = [ 'draft_odr_count' => 'Draft : Order',
						  'submitted_odr_count'  => 'Submitted : Order',
						  'approved_odr_count'  => 'Approved : Order',
						  'inprogress_odr_count'  => 'InProgress : Order',
						  'canceled_odr_count'  => 'Canceled : Order',
						  'completed_odr_count'  => 'Completed : Order',
						  'onlineunpaid_odr_count'  => 'OnlineUnpaid : Order',
						  'redesign_odr_count'  => 'Redesign : Order',
						  'draft_inv_count'  => 'Draft : Invoice',
						  'submitted_inv_count'  => 'Submitted : Invoice',
						  'redesign_inv_count'  => 'Redesign : Invoice',
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
		
		$customer_m = $this->add('xepan\commerce\Model_Reports_Customer',['from_date'=>$from_date, 'to_date'=>$this->app->nextDate($to_date), 'customer'=>$contact, 'from_amount'=>$from_amount, 'to_amount'=>$to_amount]);
		
		if($qsp_status && $order_by)
			$customer_m->setOrder($qsp_status,$order_by);	
			
		$grid = $this->add('xepan\hr\Grid',null,'view',['reports\customer']);
		$grid->setModel($customer_m,['name','id','draft_odr_count','submitted_odr_count','approved_odr_count','inprogress_odr_count','canceled_odr_count','completed_odr_count','onlineunpaid_odr_count','redesign_odr_count','draft_inv_count','submitted_inv_count','redesign_inv_count','due_inv_count','paid_inv_count','canceled_inv_count']);	
		$grid->addPaginator(30);
		$grid->addQuickSearch(['name']);

		$grid->js('click')->_selector(".col-md-3")->univ()->frameURL('Details',[[$this->api->url(),'page'=>$this->js()->_selectorThis()->closest('[data-url]')->data('url')],'customer_id'=>$this->js()->_selectorThis()->closest('[data-customer]')->data('customer'),'status'=>$this->js()->_selectorThis()->closest('[data-status]')->data('status')]);
		
		if($form->isSubmitted()){
			$form->validateFields()
				 ->reloadView($grid);
		}
		
	}

	function defaultTemplate(){
		return ['reports\pagetemplate'];
	}
}