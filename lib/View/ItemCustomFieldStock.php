<?php

namespace xepan\commerce;

class View_ItemCustomFieldStock extends \xepan\hr\Grid{

	function formatRow(){
		$transaction_row = $this->add('xepan\commerce\Model_Store_TransactionRow')
							->addCondition('item_id',$this->model->id);

		$transaction_row->addExpression('cf_name_and_value')->set(function($m,$q){
			$x = $m->add('xepan\commerce\Model_Store_TransactionRowCustomFieldValue',['table_alias'=>'contacts_str']);
			return $x->addCondition('store_transaction_row_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat(CONCAT([0],":",[1]) SEPARATOR "<br/>")',[$x->getElement('custom_name'),$x->getElement('custom_value')]));
		})->allowHTML(true);

		$transaction_row->addExpression('qty')->set(function($m,$q){
			return $q->sum('quantity');
		});
		$transaction_row->_dsql()->group('cf_name_and_value','status');

		$grid_item = $this->add('xepan\hr\Grid',null,'order_detail',['view\itemcustomfieldstock','order_detail']);
		$grid_item->setModel($transaction_row,['cf_name_and_value','qty','order_item_qty_unit','status']);

		// $order_items = $this->add('xepan\commerce\Model_QSP_Detail')
		// 				->addCondition('qsp_master_id',$this->model->id);
		// $grid_item = $this->add('xepan\hr\Grid',null,'order_detail',['view\orderpipeline','order_detail']);
		// $grid_item->setModel($order_items,['item','quantity','qty_unit_id','qty_unit']);
		// $grid_item->addColumn('expander','Timeline');

		// $grid_item->addHook('formatRow',function($g){
		// 	$order_detail = $g->add('xepan\commerce\Model_QSP_Detail')->load($g->model->id);
		// 	$array = json_decode($order_detail['extra_info']?:"[]",true);
		// 	unset($array[0]);
			
		// 	$jobcard_pipeline = $g->add('xepan\production\View_JobcardPipeline',['order_detail_id'=>$g->model->id],'production_step',['view\orderpipeline','production_step']);
		// 	$jobcard_pipeline->setSource($array);

		// 	$g->current_row_html['production_step'] = $jobcard_pipeline->getHtml();
		// });

		$this->current_row_html['order_detail'] = $grid_item->getHtml();
		parent::formatRow();
	}

	function defaultTemplate(){
		return['view\itemcustomfieldstock'];
	}
}