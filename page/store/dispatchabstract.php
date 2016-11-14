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

		$this->app->side_menu->addItem(['To Received','badge'=>[$counts_redefined['ToReceived'],'swatch'=>' label label-primary label-circle pull-right']],$this->api->url("xepan_commerce_store_dispatchrequest"));
		$this->app->side_menu->addItem(['Dispatch','badge'=>[$counts_redefined['Received'],'swatch'=>' label label-primary label-circle pull-right']],$this->api->url("xepan_commerce_store_dispatch"));
	}
}