<?php
namespace xepan\commerce;
class page_store_item extends \xepan\base\Page{
	public $title="Store Items";
	function init(){
		parent::init();

		$grid= $this->add('xepan\base\Grid');

		$opening_model = $this->add('xepan\commerce\Model_Item_Stock');
		$opening_model->addExpression('name_with_detail')->set(function($m,$q){
			return $q->expr('CONCAT([0]," :: ",[1]," :: ",IFNULL([2]," "))',[$m->getElement('name'),$m->getElement('sku'),$m->getElement('hsn_sac')]);
		});

		$grid->setModel($opening_model,['name_with_detail','opening','purchase','purchase_return','consumption_booked','consumed','received','adjustment_add','adjustment_removed','movement_in','movement_out','sales_return','shipped','delivered','package_created','package_opened','consumed_in_package','release_from_package','net_stock','qty_unit']);
		$grid->addPaginator(50);

		$qsf = $grid->addQuickSearch(['name_with_detail']);

		// $qsf->addField('CheckBox','show_zero');

		// ========= old code =======
		// $item=$this->add('xepan\commerce\Model_Item');
		// // throw new \Exception($item->ref('StoreTransactionRows')->count()->getOne(), 1);
		
		// $item->addExpression('total_in')->set(function($m,$q){
		// 	$row  = $m->add('xepan\commerce\Model_Store_TransactionRow')
		// 			->addCondition('item_id',$m->getField('id'))
		// 			->addCondition('type',"Store_Transaction");
		// 	return $q->expr("IFNULL([0],0)",[$row->sum('quantity')]);
		// })->sortable(true);

		// $item->addExpression('total_out')->set(function($m,$q){
		// 	$row  = $m->add('xepan\commerce\Model_Store_TransactionRow')
		// 			->addCondition('item_id',$m->getField('id'))
		// 			->addCondition('type','Store_DispatchRequest');

		// 	return $q->expr("IFNULL([0],0)",[$row->sum('quantity')]);
		// })->sortable(true);

		// $item->addExpression('current_stock')->set(function($m,$q){
		// 	return $q->expr("(IFNULL([0],0) - IFNULL([1],0))",[$m->getField('total_in'),$m->getField('total_out')]);
		// })->sortable(true);

		// $item->addExpression('total_in')->set(function($m,$q){
		// 	return $m->refSQL('StoreTransactionRows')->sum('quantity');
		// })->sortable(true);

		// $c=$this->add('xepan\hr\Grid',null,null,['view/store/item-grid']);
		// $c->addPaginator(10);
		// $c->addQuickSearch(['name']);
		// $c->setModel($item);
	}
}