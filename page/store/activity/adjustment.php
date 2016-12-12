<?php


namespace xepan\commerce;

class page_store_activity_adjustment extends \xepan\base\Page{
	public $title="Adjustment Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$form->addField('dropdown','adjustment_type')->setValueList(['Adjustment_Add'=>'Adjustment_Add','Adjustment_Removed'=>'Adjustment_Removed'])->setEmptyText('Please select adjustment type');

		$warehouse_field = $form->addField('dropdown','warehouse');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addSubmit('save');
		
		$grid= $this->add('xepan\base\Grid');
		$item_stock_model = $this->add('xepan\commerce\Model_Item_Stock')->addCondition([['adjustment_add','>',0],['adjustment_removed','>',0]]);
		$grid->setModel($item_stock_model,['name','adjustment_add','adjustment_removed','purchase','consumed','consumption_booked','received','net_stock']);

		if($form->isSubmitted()){
			if($form['adjustment_type'] == '')
				$form->displayError('adjustment_type','Please select adjustment type');

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info'],true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],$form['adjustment_type']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,$form['adjustment_type']);
		}

		$transaction_row_m = $this->add('xepan\commerce\Model_Store_TransactionRow'); 
		$transaction_row_m->addCondition('status','Adjustment_Add');
		
		$this->add('Grid')->setModel($transaction_row_m);
	}
}