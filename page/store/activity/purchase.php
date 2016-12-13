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
		
		$grid= $this->add('xepan\base\Grid');
		$item_stock_model = $this->add('xepan\commerce\Model_Item_Stock')->addCondition('purchase','>',0);
		$grid->setModel($item_stock_model,['name','purchase','consumed','consumption_booked','received','net_stock']);

		if($form->isSubmitted()){
			$cf_key = $this->add('xepan\commerce\Model_Item')->load($form['item'])->convertCustomFieldToKey(json_decode($form['extra_info']?:'{}',true));
			
			$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse')->load($form['warehouse']);
			$transaction = $warehouse->newTransaction(null,null,$form['supplier'],'Purchase',null,$form['warehouse']);
			$transaction->addItem(null,$form['item'],$form['quantity'],null,$cf_key,'Purchase');
			
			$js = [$grid->js()->reload(),$form->js()->reload()];
			$form->js(null,$js)->univ()->successMessage('saved')->execute();
		}
	}
}