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
		$array= [];
		$extra_info = json_decode($this->model['extra_info'],true);
		$item = $this->model->item();
		$arr = $item->getStockAvalibility($this->model['extra_info'],$this->model['quantity'],$array);
		
		$cf_key = $item->convertCustomFieldToKey($extra_info,true);
		$this->current_row_html['avail_quantity'] = $arr[$item['name']][$cf_key]['available'];
		
		if(isset($arr[$item['name']])){
			$this->current_row_html['item_name'] = $item['name'];
			unset($arr[$item['name']]);
		}


		$cf_array = $item->convertCFKeyToArray($cf_key);
		// var_dump($arr);
		$cf_list = $this->add('CompleteLister',null,'stock_effected_cf',['view/stock_availibility','stock_effected_cf']);
		$cf_list->setSource($cf_array);
		$this->current_row_html['stock_effected_cf'] = $cf_list->getHtml();

		$consumption_item_list = $this->add('CompleteLister',null,'consumption_item',['view/stock_availibility','consumption_item']);
		$consumption_item_list->setSource($arr);
		$this->current_row_html['consumption_item'] = $consumption_item_list->getHtml();
		parent::formatRow();

	}

	function defaultTemplate(){
		return ['view/stock_availibility'];
	}


}