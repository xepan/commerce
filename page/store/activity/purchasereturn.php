<?php


namespace xepan\commerce;

class page_store_activity_purchasereturn extends \xepan\base\Page{
	public $title="Purchase Returned Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		
		$warehouse_field = $form->addField('dropdown','warehouse','To Warehouse');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		
		$supplier_field = $form->addField('DropDown','supplier');
		$supplier_field->setModel('xepan\commerce\Model_Supplier');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addSubmit('save');
		
		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey($form['extra_info']);
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Purchase_Return');
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Opening');
		}

	}
}