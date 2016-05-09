<?php
namespace xepan\commerce;
class page_store_item extends \xepan\base\Page{
	public $title="Store Items";
	function init(){
		parent::init();

		$item=$this->add('xepan\commerce\Model_Item');
		// throw new \Exception($item->ref('StoreTransactionRows')->count()->getOne(), 1);
		
		$item->addExpression('total_in')->set(function($m,$q){
			$row  = $m->add('xepan\commerce\Model_Store_TransactionRow')
					->addCondition('item_id',$m->getField('id'))
					->addCondition('document_type',"Purchase");
			return $q->expr("IFNULL([0],0)",[$row->sum('qty')]);
		})->sortable(true);

		$item->addExpression('total_out')->set(function($m,$q){
			$row  = $m->add('xepan\commerce\Model_Store_TransactionRow')
					->addCondition('item_id',$m->getField('id'))
					->addCondition('document_type',['Sale','Dispatch']);

			return $q->expr("IFNULL([0],0)",[$row->sum('qty')]);
		})->sortable(true);

		$item->addExpression('current_stock')->set(function($m,$q){
			return $q->expr("(IFNULL([0],0) - IFNULL([1],0))",[$m->getField('total_in'),$m->getField('total_out')]);
		})->sortable(true);

		$item->addExpression('total_in')->set(function($m,$q){
			return $m->refSQL('StoreTransactionRows')->sum('qty');
		})->sortable(true);

		$c=$this->add('xepan\hr\CRUD',null,null,['view/store/item-grid']);
		$c->grid->addPaginator(10);
		$c->grid->addQuickSearch(['name']);
		$c->setModel($item);
	}
}