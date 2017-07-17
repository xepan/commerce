<?php


namespace xepan\commerce;

class page_store_activity_opening extends \xepan\base\Page{

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$warehouse_field = $form->addField('dropdown','warehouse')->validate('required');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$warehouse_field->setEmptyText('please select');

		$item_model = $this->add('xepan\commerce\Model_Store_Item');
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel($item_model);
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('text','extra_info');
		
		$form->addField('Number','quantity');
		$form->addField('text','narration');

		$this->add('View')->setElement('h2')->setHtml('<br/>Opening Stock');
		$grid = $this->add('xepan\base\Grid');

		$opening_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Opening');
		// $opening_model->getElement('transaction_narration')->caption('narration');
		$grid->setModel($opening_model,['item_name','quantity','transaction_narration','from_warehouse','to_warehouse','created_at']);
		$grid->addPaginator($ipp=15);
		$grid->addQuickSearch(['item_name']);

		$form->addSubmit('Save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode(($form['extra_info']?:'{}'),true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Opening',null,null,$form['narration']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Opening');
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}