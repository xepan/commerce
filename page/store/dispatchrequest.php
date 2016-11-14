<?php

namespace xepan\commerce;

class page_store_dispatchrequest extends \xepan\commerce\page_store_dispatchabstract{
	public $title="Dispatch Request Item";

	function init(){
		parent::init();

		$dispatch = $this->add('xepan\commerce\Model_Store_DispatchRequest');
		$dispatch->addCondition('status','ToReceived');

		$dispatch->setOrder('related_document_id','desc');
		$dispatch->setOrder('id','desc');
		
		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false],null,['view/store/dispatch-request-grid']);
		$crud->setModel($dispatch);
		
		$crud->grid->addQuickSearch(['jobcard']);
		if(!$crud->isEditing()){
			
			$crud->grid->js('click')->_selector('.do-dispatch-item-jobcard-view')->univ()->frameURL('Item Jobcard Details',[$this->api->url('xepan_production_jobcarddetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-jobcard-id]')->data('jobcard-id')]);
			$crud->grid->js('click')->_selector('.do-dispatch-order-item-view')
				->univ()->frameURL('Order Item Details',
					[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-related-document-id]')->data('related-document-id')]);
		}

		$crud->grid->addHook('formatRow',function($g){
			if($g->model['status'] == "Received"){
				$g->current_row_html['item_dispatch_qty'] = $g->model['received'];
			}elseif($g->model['status']=="ToReceived"){
				$g->current_row_html['item_dispatch_qty'] = $g->model['toreceived'];
			}else{
				$g->current_row_html['item_dispatch_qty'] = $g->model['item_quantity'];
			}
			
			$g->current_row_html['edit'] = " ";
			$g->current_row_html['delete'] = " ";

		});

		$crud->grid->addPaginator($ipp=30);
	}
}