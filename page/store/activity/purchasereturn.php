<?php


namespace xepan\commerce;

class page_store_activity_purchasereturn extends \xepan\base\Page{
	public $title="Purchase Return Order Item";

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
		$form->addField('text','narration');
		$form->addSubmit('save');
		
		$this->add('View')->setElement('h2')->set('Purchase Return Stock');
		$grid= $this->add('xepan\base\Grid');
		$purchase_return_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Purchase_Return');
		$grid->setModel($purchase_return_model,['item','quantity','transaction_narration','from_warehouse']);
		$grid->addPaginator($ipp=30);


		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Purchase_Return',null,$form['supplier'],$form['narration']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Purchase_Return');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}
	}
}