<?php

namespace xepan\commerce;

class page_store_dispatch extends \Page{
	public $title="Dispatch Item";
	function init(){
		parent::init();

		$dispatch=$this->add('xepan\commerce\Model_Store_DispatchRequest');

		$c=$this->add('xepan\hr\CRUD',null,null,['view/store/dispatch-grid']);
		$c->setModel($dispatch);
	}
}