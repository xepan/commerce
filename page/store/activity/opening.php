<?php


namespace xepan\commerce;

class page_store_activity_opening extends \xepan\base\Page{

	function init(){
		parent::init();
		
		$form = $this->add('Form');
		$warehouse_field = $form->addField('dropdown','warehouse');
		$warehouse_field->setModel('xepan\commerce\Model_Store_Warehouse');
		$item_field = $form->addField('xepan\commerce\Item','item');
		$item_field->setModel('xepan\commerce\Item');
		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		$form->addField('text','extra_info');
		$form->addField('Number','quantity');


		$this->add('View')->setElement('h2')->set('Opening Stock');
		$grid= $this->add('xepan\base\Grid');

		$opening_model = $this->add('xepan\commerce\Model_Item_Stock')->addCondition('opening','>',0);
		$grid->setModel($opening_model,['name','opening','purchase','consumed','consumption_booked','received','net_stock','qty_unit']);
		$grid->addPaginator($ipp=30);

		$form->addSubmit('Save')->addClass('btn btn-primary');
		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode(($form['extra_info']?:'{}'),true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['warehouse'],'Opening');
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Opening');
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}

	}
}