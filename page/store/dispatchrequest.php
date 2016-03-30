<?php

namespace xepan\commerce;

class page_store_dispatchrequest extends \Page{
	public $title="Dispatch Request";
	function init(){
		parent::init();

		$dispatch=$this->add('xepan\commerce\Model_Store_DispatchRequest');

		$c=$this->add('xepan\hr\CRUD',null,null,['view/store/dispatch-request-grid']);
		$c->setModel($dispatch);
	}
}