<?php
namespace xepan\commerce;
class page_store_item extends \Page{
	public $title="Store Items";
	function init(){
		parent::init();
		$item=$this->add('xepan\commerce\Model_Item');

		$c=$this->add('xepan\hr\CRUD',null,null,['view/store/item-grid']);
		$c->setModel($item);
	}
}