<?php

namespace xepan\commerce;

class page_store_dispatch extends \xepan\commerce\page_store_dispatchabstract{
	public $title="Dispatch Order Item";
	public $record_status='dispatch';

	function init(){
		parent::init();

		$order_dispatch_m = $this->add('xepan\commerce\Model_Store_OrderItemDispatch');
		$order_dispatch_m->addCondition('due_quantity','>',0);
		$order_dispatch_m->setOrder('id','desc');
		
		$grid = $this->add('xepan\hr\Grid',null,null,['view/store/dispatch']);
		$grid->setModel($order_dispatch_m);

		$grid->js('click')->_selector('.do-dispatch-order-detail-view')
			->univ()->frameURL('Sale Order',
				[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-related-document-id]')->data('related-document-id')]);

		$grid->addHook('formatRow',function($g){
			// custom field
			$array = json_decode($g->model['extra_info']?:"[]",true);
			$cf_html = " ";
			foreach ($array as $department_id => &$details) {
				$department_name = $details['department_name'];
				$cf_list = $g->add('CompleteLister',null,'extra_info',['view\qsp\extrainfo']);
				$cf_list->template->trySet('department_name',$department_name);
				$cf_list->template->trySet('narration',$details['narration']);
				unset($details['department_name']);
				$cf_list->setSource($details);
				$cf_html  .= $cf_list->getHtml();
			}

			if($cf_html != " "){
				$cf_html = "<br/>".$cf_html;
			}

			$g->current_row_html['extra_info'] = $cf_html;

			$multiplier = $g->app->getUnitMultiplier($g->model['item_qty_unit_id'],$g->model['qty_unit_id']);
			$g->current_row_html['order_unit_based_due_quantity'] = $g->model['due_quantity'] / $multiplier;

			$g->current_row_html['order_unit_based_toreceived_quantity'] = $g->model['toreceived_quantity'] / $multiplier;
			$g->current_row_html['order_unit_based_received_quantity'] = $g->model['received_quantity'] / $multiplier;
			$g->current_row_html['order_unit_based_shipped_quantity'] = $g->model['shipped_quantity'] / $multiplier;
			$g->current_row_html['order_unit_based_delivered_quantity'] = $g->model['delivered_quantity'] / $multiplier;
			// $item_model = $g->add('xepan\commerce\Model_Item')->load($g->model['item_id']);
			// $spec_array = $item_model->getSpecification('exact');
		});



		$grid->add('xepan\hr\Controller_ACL');
		$grid->addPaginator(30);

	}
}