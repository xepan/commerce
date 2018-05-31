<?php

namespace xepan\commerce;

class page_store_dispatchdelivered extends \xepan\commerce\page_store_dispatchabstract{
	public $title="Order Delivered";

	public $record_status='delivered';

	function init(){
		parent::init();
		
		$dispatch = $this->add('xepan\commerce\Model_Store_Delivered');
		$dispatch->addCondition('status','Delivered');
		$dispatch->setOrder('id','desc');

		$dispatch->getElement("related_document_no")->caption('Sale Order');
		$dispatch->getElement("to_contact_name")->caption('customer');
		$dispatch->getElement("delivery_via")->caption('Delivery Detail');
		$dispatch->getElement("created_at")->caption('Created');

		$crud = $this->add('xepan\hr\CRUD');
		$crud->grid->fixed_header = false;
		$crud->setModel($dispatch,['related_document_no','related_document_id','to_contact_name','from_warehouse','jobcard','created_by','created_at','delivery_via','delivery_reference','shipping_address','shipping_charge','narration','tracking_code']);		

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row_html['delivery_via'] = "Delivery Via: ".$g->model['delivery_via']."<br/>Delivery Reference: ".$g->model['delivery_reference'];
			$g->current_row_html['created_at'] = "Date: ".$g->model['created_at']."<br/>By: ".$g->model['created_by'];
			$g->current_row_html['related_document_no'] = '<a class="do-dispatch-order-item-view" data-related-document-id="'.$g->model['related_document_id'].'">'.$g->model['related_document_no'].'</a>';
		});

		// $grid->js('click')->_selector('.do-dispatch-item-jobcard-view')->univ()->frameURL('Item Jobcard Details',[$this->api->url('xepan_production_jobcarddetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-jobcard-id]')->data('jobcard-id')]);
		$crud->js('click')->_selector('.do-dispatch-order-item-view')
			->univ()->frameURL('Order Item Details',
				[$this->api->url('xepan_commerce_quickqsp'),'document_id'=>$this->js()->_selectorThis()->attr('data-related-document-id'),'readmode'=>true,'document_type'=>'SalesOrder']);

		$crud->grid->addPaginator($ipp=50);
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('related_document_id');
		$crud->grid->removeColumn('delivery_reference');
		$crud->grid->removeColumn('delivery');
		$crud->grid->removeColumn('created_by');
	}
}