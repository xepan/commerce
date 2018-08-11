<?php
namespace xepan\commerce;

class page_store_transaction extends \xepan\base\Page{
	public $title="Store Transaction";
	function init(){
		parent::init();

		$transaction = $this->add('xepan\commerce\Model_Store_Transaction');
		$transaction->addCondition([['jobcard_id',null],['jobcard_id',0]]);
		$transaction->setOrder('id','desc');

		$transaction->actions = [
				'ToReceived'=>['view','details','delete'],
				'Received'=>['view','details','delete']
			];

		$crud = $this->add('xepan\hr\CRUD',['allow_add'=>false,'allow_edit'=>false]);
		$crud->setModel($transaction,null,['branch','from_warehouse','to_warehouse','created_by','type','created_at','status','narration','item_quantity','toreceived','received']);
		$crud->grid->addPaginator(25);
		$crud->grid->addQuickSearch(['from_warehouse','to_warehouse','type']);
		$crud->add('xepan\base\Controller_MultiDelete');
		$crud->grid->removeAttachment();

	}
}