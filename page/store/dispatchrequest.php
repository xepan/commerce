<?php

namespace xepan\commerce;

class page_store_dispatchrequest extends \xepan\base\Page{
	public $title="Dispatch Request Item";
	function init(){
		parent::init();

		$dispatch = $this->add('xepan\commerce\Model_Store_DispatchRequest');
		$dispatch->setOrder('id','desc');
		$dispatch->setOrder('related_document_id','desc');
		$c = $this->add('xepan\hr\CRUD',null,null,['view/store/dispatch-request-grid']);
		$c->setModel($dispatch);
		$c->grid->addQuickSearch(['jobcard']);
		if(!$c->isEditing()){
			$c->grid->js('click')->_selector('.do-dispatch-item-jobcard-view')->univ()->frameURL('Item Jobcard Details',[$this->api->url('xepan_production_jobcarddetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-jobcard-id]')->data('jobcard-id')]);
			$c->grid->js('click')->_selector('.do-dispatch-order-item-view')->univ()->frameURL('Order Item Details',[$this->api->url('xepan_commerce_salesorderdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-related-document-id]')->data('related-document-id')]);
		}
	}
}