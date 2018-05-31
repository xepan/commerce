<?php

namespace xepan\commerce;

class page_store_dispatchabstract extends \xepan\base\Page{
	public $title="Dispatch Request Management";

	function init(){
		parent::init();

		$count_m = $this->add('xepan\commerce\Model_Store_TransactionRow');
		$counts = $count_m->_dsql()->del('fields')->field('status')->field('count(*) counts')->group('Status')->get();
		$counts_redefined =[];
		$total=0;
		foreach ($counts as $cnt) {
			$counts_redefined[$cnt['status']] = $cnt['counts'];
			$total += $cnt['counts'];
		}


		$order_dispatch_m = $this->add('xepan\commerce\Model_Store_OrderItemDispatch');

		$order_dispatch_m->addCondition('due_quantity','>',0);
		$order_dispatch_m->_dsql()->group('qsp_master_id');
		$total_order_to_dispatch = $order_dispatch_m->count()->getOne()?:0;

		$status=$this->record_status;

		$this->app->page_top_right_button_set->addButton(["To Received (".$counts_redefined['ToReceived'].")"])
				->addClass('btn btn-'.($this->record_status=='ToReceived'?'success':'primary'))
				->js('click')->univ()->location($this->api->url('xepan_commerce_store_dispatchrequest',['status'=>null]))
				;
		$this->app->page_top_right_button_set->addButton(["Dispatch ($total_order_to_dispatch)"])
				->addClass('btn btn-'.($this->record_status=='dispatch'?'success':'primary'))
				->js('click')->univ()->location($this->api->url('xepan_commerce_store_dispatch',['status'=>null]))
				;
		$this->app->page_top_right_button_set->addButton(["Under Shipping (TODO)"])
				->addClass('btn btn-'.($this->record_status=='undershipping'?'success':'primary'))
				->js('click')->univ()->location($this->api->url('xepan_commerce_store_undershipping',['status'=>null]))
				;
		$this->app->page_top_right_button_set->addButton(["Delivered (TODO)"])
				->addClass('btn btn-'.($this->record_status=='delivered'?'success':'primary'))
				->js('click')->univ()->location($this->api->url('xepan_commerce_store_dispatchdelivered',['status'=>null]))
				;

		// $this->app->side_menu->addItem(['To Received','icon'=>'fa fa fa-angle-double-right text-primary','badge'=>[$counts_redefined['ToReceived']?:0,'swatch'=>' label label-primary label-circle pull-right']],$this->api->url("xepan_commerce_store_dispatchrequest"));
		// $this->app->side_menu->addItem(['Dispatch','icon'=>'fa fa-truck text-success','badge'=>[$total_order_to_dispatch,'swatch'=>' label label-primary label-circle pull-right']],$this->api->url("xepan_commerce_store_dispatch"));
		// $this->app->side_menu->addItem(['Under Shipping','icon'=>'fa fa-road text-warning','badge'=>[0,'swatch'=>' label label-primary label-circle pull-right']],$this->api->url("xepan_commerce_store_undershipping"));
		// $this->app->side_menu->addItem(['Delivered','icon'=>'fa fa-truck text-success','badge'=>[0,'swatch'=>' label label-primary label-circle pull-right']],$this->api->url("xepan_commerce_store_dispatchdelivered"));

	}
}