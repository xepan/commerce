<?php
namespace xepan\commerce;

class page_store_report extends \xepan\base\Page{
	public $title="Store Dashboard";
	function init(){
		parent::init();

		$item = $this->add('xepan\commerce\Model_Item');

		$form = $this->add('Form',null,null,['form/empty']);
		$form->addField('Dropdown','item_id')->setModel($item);

		$transaction=$this->add('xepan\commerce\Model_Store_TransactionRow');

		$grid = $this->add('xepan\hr\Grid');

		$form->addSubmit('Get Report');

		if($_GET['filter']){
			$item = $this->app->stickyGET('item_id');
			$transaction->addCondition('item_id',$_GET['item_id']);
			// throw new \Exception($transaction->count()->getOne(), 1);
			
		}/*else{
			$transaction->addCondition('id',-1);
		}*/

		$transaction->addExpression('sum_qty')->set(function($m,$q){
			return $m->_dsql()->group('from_warehouse')->sum($m->getElement('quantity'));
		})->caption('quantity');
		$grid->setModel($transaction,['from_warehouse','item_name','sum_qty']);
		$grid->addQuickSearch(['from_warehouse','item_name']);

		if($form->isSubmitted()){
			$grid->js()->reload(
									[
										'item_id'=>$form['item_id'],
										'filter'=>1
									]
									)->execute();
		}
	}
}