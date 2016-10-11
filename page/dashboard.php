<?php

namespace xepan\commerce;

class page_dashboard extends \xepan\base\Page{
	public $title = "Dashboard";	
	function init(){
		parent::init();


		$sale_order = $this->add('xepan\commerce\Model_SalesOrder');
		$sale_order->addCondition('created_at','>=',$this->app->today);
		
		$sale_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$sale_invoice->addCondition('created_at','>=',$this->app->today);

		$this->template->trySet('todays_orders',$sale_order->count());
		$this->template->trySet('todays_invoices',$sale_invoice->count());
			
		$sale_invoice->addCondition('status','Paid');
		
		if(!$sale_invoice->sum('net_amount'))
			$this->template->trySet('todays_payments',0);
		else
			$this->template->trySet('todays_payments',$sale_invoice->sum('net_amount')?0:0);
				
		$unpaid_sale_invoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$unpaid_sale_invoice->addCondition('created_at','>=',$this->app->today);		
		$sale_invoice->addCondition('status','Due');
		
		if(!$unpaid_sale_invoice->sum('net_amount')->getOne())
			$this->template->trySet('remaining_payments',0);
		else
			$this->template->trySet('remaining_payments',$unpaid_sale_invoice->sum('net_amount'));

		$so = $this->add('xepan\commerce\Model_SalesOrder');
		$so->setOrder('created_at','desc');
		$so->setLimit(50);
		$order_grid = $this->add('xepan\base\Grid',null,'order_grid',['view\dashboard\order']);
		$order_grid->setModel($so);
		$order_grid->template->trySet('heading','Recent Orders');
		$order_grid->addPaginator('5');
		
		$inv = $this->add('xepan\commerce\Model_SalesInvoice');
		$inv->setOrder('due_date','asc');
		$inv->addCondition('status','Due');
		$invoice_grid = $this->add('xepan\base\Grid',null,'invoice_grid',['view\dashboard\order']);
		$invoice_grid->setModel($inv);
		$invoice_grid->template->trySet('heading','Due Invoices');
		$invoice_grid->addPaginator('5');

		$detail = $this->add('xepan\commerce\Model_QSP_Detail');
		$detail->addExpression('count','count(*)');
		$detail->_dsql()->group('item_id');
		$detail->setOrder('count','desc');
		$detail->setLimit(5);
		$this->add('xepan\hr\Grid',null,'favourite_item',['view\dashboard\favitem'])->setModel($detail,['item','count']);
	
		$new_customer = $this->add('xepan\commerce\Model_Customer');
		$new_customer->addCondition('created_at','>=',$this->app->today);
		$this->template->trySet('customers',$new_customer->count());

		$new_customer = $this->add('xepan\commerce\Model_Customer');
		$new_customer->addCondition('created_at','>=',$this->app->today);
		$new_customer->addExpression('count','count(DISTINCT country_id)');
		$this->template->trySet('countries',$new_customer->tryLoadAny()->get('count'));
	
		$order = $this->add('xepan\commerce\Model_SalesOrder');
		$order->addExpression('customer_created_at')->set(function($m,$q){
			return $q->expr('date([0])',[$m->refSQL('contact_id')->fieldQuery('created_at')]) ;
		});

		$order->addCondition('customer_created_at',$this->app->today);
		$this->template->trySet('new_orders',$order->count()->getOne());


	}

	function defaultTemplate(){
		return['page/dashboard/commerce'];
	}
}