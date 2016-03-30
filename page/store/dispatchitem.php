<?php
namespace xepan\commerce;

class page_store_dispatchitem extends \Page{
	public $title="Dispatch Item";
	function init(){
		parent::init();

		$dispatch_item=$this->add('xepan\commerce\Model_Store_DispatchRequest');

		$g=$this->add('xepan\hr\CRUD',null,null,['view/store/dispatch-item-grid']);
		$g->setModel($dispatch_item);

	}
}