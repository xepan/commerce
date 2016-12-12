<?php


namespace xepan\commerce;

class page_store_activity_movement extends \xepan\base\Page{
	public $title="Purchase Return Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');

		$from_warehouse_field = $form->addField('dropdown','from_warehouse');
		$from_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');

		$to_warehouse_field = $form->addField('dropdown','to_warehouse');
		$to_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addSubmit('save');
		
		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['from_warehouse']);
			
			$transaction = $warehouse->newTransaction(null,null,$form['from_warehouse'],'Movement',null,$form['to_warehouse']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'ToReceived');
		}

		$transaction_row_m = $this->add('xepan\commerce\Model_Store_TransactionRow'); 
		$transaction_row_m->addCondition('status','ToReceived');
		
		$this->add('Grid')->setModel($transaction_row_m);
	}
}