<?php

namespace xepan\commerce;

class page_store_dispatchrequest extends \xepan\commerce\page_store_dispatchabstract{
	public $title="Dispatch Request Item";

	public $record_status='ToReceived';

	function init(){
		parent::init();

		$dispatch = $this->add('xepan\commerce\Model_Store_DispatchRequest');
		$dispatch->addCondition('status','ToReceived');

		$dispatch->setOrder('related_document_id','desc');
		$dispatch->setOrder('id','desc');
		
		$grid = $this->add('xepan\hr\Grid',null,null,['view/store/dispatch-request-grid']);
		$grid->setModel($dispatch);
		$grid->add('xepan\hr\Controller_ACL');

		$grid->addQuickSearch(['jobcard']);			
		$grid->js('click')->_selector('.do-dispatch-item-jobcard-view')->univ()->frameURL('Item Jobcard Details',[$this->api->url('xepan_production_jobcarddetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-jobcard-id]')->data('jobcard-id')]);
		$grid->js('click')->_selector('.do-dispatch-order-item-view')
			->univ()->frameURL('Order Item Details',
				[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-related-document-id]')->data('related-document-id')]);

		$grid->addHook('formatRow',function($g){
			if($g->model['status'] == "Received"){
				$g->current_row_html['item_dispatch_qty'] = $g->model['received'];
			}elseif($g->model['status']=="ToReceived"){
				$g->current_row_html['item_dispatch_qty'] = $g->model['toreceived'];
			}else{
				$g->current_row_html['item_dispatch_qty'] = $g->model['item_quantity'];
			}
		});

		$grid->addPaginator($ipp=30);
	}
}