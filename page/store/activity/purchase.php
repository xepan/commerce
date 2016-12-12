<?php


namespace xepan\commerce;

class page_store_activity_purchase extends \xepan\base\Page{
	public $title="Purchase Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		
		$supplier_field = $form->addField('DropDown','supplier');
		$supplier_field->setModel('xepan\commerce\Model_Supplier');

		$warehouse_field = $form->addField('dropdown','warehouse');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addSubmit('save');
		
		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['supplier'],'Purchase',null,$form['warehouse']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Purchase');
		}

		$transaction_row_m = $this->add('xepan\commerce\Model_Store_TransactionRow'); 
		$transaction_row_m->addCondition('status','Purchase');
		
		$this->add('Grid')->setModel($transaction_row_m);
	}
}