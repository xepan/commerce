<?php


namespace xepan\commerce;

class page_store_activity_purchase extends \xepan\base\Page{
	public $title="Purchase Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		
		$supplier_model = $this->add('xepan\commerce\Model_Supplier');
		$supplier_model->title_field = "effective_name";

		$supplier_field = $form->addField('DropDown','supplier')->validate('required');
		$supplier_field->setModel($supplier_model);
		$supplier_field->setEmptyText('Please Select');

		$warehouse_model = $this->add('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field = $form->addField('dropdown','warehouse')->validate('required');
		$warehouse_field->setModel($warehouse_model);
		$warehouse_field->setEmptyText('Please Select');
				
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Store_Item');
		$item_field->other_field->validate('required');

		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addField('text','narration');
		$form->addSubmit('save');
		
		$this->add('View')->setElement('h2')->set('Purchase Stock');
		$grid= $this->add('xepan\base\Grid');
		$purchase_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Purchase');
		$grid->setModel($purchase_model,['item_name','quantity','transaction_narration','to_warehouse','created_at']);
		$grid->addPaginator($ipp=30);
		$grid->addQuickSearch(['item_name']);

		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['supplier'],'Purchase',null,$form['warehouse'],$form['narration']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Purchase');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}
	}
}