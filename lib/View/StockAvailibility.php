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
		
		$arr = $item->getStockAvalibility($this->model['extra_info'],$this->model['quantity'],$array,null,$this->model['qty_unit_id']);
		
		$cf_key = $item->convertCustomFieldToKey($extra_info,true);

		if(isset($arr[$item['name']])){
			$this->current_row_html['item_name'] = $item['name'];
			$this->current_row_html['avail_quantity'] = $arr[$item['name']][$cf_key]['available'];
			$this->current_row_html['available_unit'] = $arr[$item['name']][$cf_key]['unit'];

			$this->current_row_html['required_quantity'] = $arr[$item['name']][$cf_key]['required'];
			$this->current_row_html['required_unit'] = $arr[$item['name']][$cf_key]['unit'];
			unset($arr[$item['name']]);
		}


		$cf_array = $item->convertCFKeyToArray($cf_key);
		$cf_list = $this->add('CompleteLister',null,'stock_effected_cf',['view/stock_availibility','stock_effected_cf']);
		$cf_list->setSource($cf_array);
		$this->current_row_html['stock_effected_cf'] = $cf_list->getHtml();

		//for consumption item list
		$consumption_item_list = $this->add('CompleteLister',null,'consumption_item',['view/stock_availibility','consumption_item']);
		$arr = $this->createDataArray($arr);
		$consumption_item_list->setSource($arr);

		$this->current_row_html['consumption_item'] = $consumption_item_list->getHtml();
		parent::formatRow();

	}

	function defaultTemplate(){
		return ['view/stock_availibility'];
	}

	/**
		[
			[quartz grain 0.1-0.4 mm] => Array
		        (
		            [0] => Array
		                (
		                    [required] => 1800
		                    [available] => 0
		                )
		            [cf_key] => Array
		                (
		                    [required] => 1200
		                    [available] => 60
		                )
		
		        )
		]
	*/

	/** return
		[
			['name'=>'quartz grain 0.1-0.4 mm','cf_key'=>0,'required'=>1800,'available'=>0],
			['name'=>'quartz grain 0.1-0.4 mm','cf_key'=>'cf_key','required'=>1200,'available'=>60]
		]
	*/
	function createDataArray($stock_array){
		$data_array = [];
		foreach ($stock_array as $item_name => $cf_key_array) {
			$temp = ['name'=>$item_name];

			foreach ($cf_key_array as $cf_key => $stock_array) {
				$temp['cf_key'] = $cf_key;
				$temp['required'] = $stock_array['required'];
				$temp['available'] = $stock_array['available'];
				$temp['to_purchase'] = ($stock_array['required'] <= $stock_array['available'])?0:($stock_array['required'] - $stock_array['available']);
				$temp['unit'] = $stock_array['unit'];
			}
			
			$data_array[] = $temp;
		}

		return $data_array;
	}

}