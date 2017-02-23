<?php

namespace xepan\commerce;

class page_store_reports_itemstock extends \xepan\commerce\page_store_reports_storereportsidebar{

	public $title = "Stock Item";

	function init(){
		parent::init();

		$form = $this->add('Form');

		// $grid= $this->add('xepan\hr\Grid',null,null,['page\store\reports\itemstock']);
		$grid= $this->add('xepan\hr\Grid');
		$opening_model = $this->add('xepan\commerce\Model_Item_Stock');
		$grid->setModel($opening_model,['name','opening','purchase','purchase_return','consumed','consumption_booked','received','adjustment_add','adjustment_removed','issue','issue_submitted','sales_return','movement_in','movement_out','shipped','delivered','net_stock','qty_unit_id','qty_unit']);
		
		$grid->js('click')->_selector('.do-view-opening')->univ()->frameURL('Stock Item Detail',[$this->api->url('xepan_commerce_store_reports_stockitemdetail'),'type'=>$this->js()->_selectorThis()->closest('[data-type]')->data('type'),'item_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		$grid->js('click')->_selector('.do-view-purchase')->univ()->frameURL('Stock Item Detail',[$this->api->url('xepan_commerce_store_reports_stockitemdetail'),'type'=>$this->js()->_selectorThis()->closest('[data-type]')->data('type'),'item_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		$grid->js('click')->_selector('.do-view-consumed')->univ()->frameURL('Stock Item Detail',[$this->api->url('xepan_commerce_store_reports_stockitemdetail'),'type'=>$this->js()->_selectorThis()->closest('[data-type]')->data('type'),'item_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		$grid->js('click')->_selector('.do-view-consumption_booked')->univ()->frameURL('Stock Item Detail',[$this->api->url('xepan_commerce_store_reports_stockitemdetail'),'type'=>$this->js()->_selectorThis()->closest('[data-type]')->data('type'),'item_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		$grid->js('click')->_selector('.do-view-received')->univ()->frameURL('Stock Item Detail',[$this->api->url('xepan_commerce_store_reports_stockitemdetail'),'type'=>$this->js()->_selectorThis()->closest('[data-type]')->data('type'),'item_id'=>$this->js()->_selectorThis()->closest('[data-id]')->data('id')]);
		
		$this->js(true,
			"$('#page-wrapper').addClass('nav-small');"
		);
	}
}