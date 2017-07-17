<?php


namespace xepan\commerce;

class page_store_activity_movement extends \xepan\base\Page{
	public $title="Purchase Return Order Item";

	function init(){
		parent::init();
		
		$form = $this->add('Form');

		$from_warehouse_field = $form->addField('dropdown','from_warehouse')->validate('required');
		$from_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$from_warehouse_field->setEmptyText('Please Select');

		$to_warehouse_field = $form->addField('dropdown','to_warehouse')->validate('required');
		$to_warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$to_warehouse_field->setEmptyText('Please Select');
		
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Store_Item');
		$item_field->other_field->validate('required');

		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('Number','quantity');
		$form->addField('text','extra_info');
		$form->addField('text','narration');
		$form->addSubmit('save');
		
		$this->add('View')->setElement('H2')->set("Stock Movement Record");
		$grid= $this->add('xepan\base\Grid');
		$movement_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->addCondition('type','Movement');
		$grid->setModel($movement_model,['item_name','quantity','transaction_narration','from_warehouse','to_warehouse']);
		$grid->addPaginator($ipp=25);

		if($form->isSubmitted()){

			if($form['from_warehouse'] == $form['to_warehouse'])
				$form->error('from_warehouse','From and To warehouse must not be same');

			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['from_warehouse']);
			
			$transaction = $warehouse->newTransaction(null,null,$form['from_warehouse'],'Movement',null,$form['to_warehouse']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'ToReceived',$form['narration']);
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

		
		// $transaction_row_m = $this->add('xepan\commerce\Model_Store_TransactionRow'); 
		// $transaction_row_m->addCondition('status','ToReceived');		
		// $this->add('Grid')->setModel($transaction_row_m);
	}
}