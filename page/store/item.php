<?php
namespace xepan\commerce;
class page_store_item extends \Page{
	public $title="Store Items";
	function init(){
		parent::init();
		$item=$this->add('xepan\commerce\Model_Item')->load(1025);
		// throw new \Exception($item->ref('StoreTransactionRows')->count()->getOne(), 1);
		
		$item->addExpression('total_in')->set(function($m,$q){
			return $m->refSQL('StoreTransactionRows')->sum('qty');

			return "''";
		});


		$c=$this->add('xepan\hr\CRUD',null,null,['view/store/item-grid']);
		$c->setModel($item);
	}
}