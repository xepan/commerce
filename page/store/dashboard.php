<?php
namespace xepan\commerce;

class page_store_dashboard extends \xepan\base\Page{
	public $title="Store Dashboard";
	function init(){
		parent::init();
		$item=$this->add('xepan\commerce\Model_Item');
		// throw new \Exception($item->ref('StoreTransactionRows')->count()->getOne(), 1);
		
		$c=$this->add('xepan\hr\Grid',null,null,['view/store/item-grid']);
		// $c=$this->add('Grid');
		$c->addPaginator(5);
		$c->addQuickSearch(['name']);
		$c->setModel($item)->setOrder('quantity','asc');
		// $c->setModel($item);
	}
}