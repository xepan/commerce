<?php

namespace xepan\commerce;

/**
* 
*/
class View_StockAvailibility extends \xepan\base\Grid{
	public $sale_order_id;
	function init(){
		parent::init();

		// $this->addClass('consumption-item-view');
		// $this->js('reload')->reload();

		// $this->add('View_Info')->set($this->sale_order_id);
	}

	function formatRow(){

		parent::formatRow();
	}


}