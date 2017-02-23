<?php

namespace xepan\commerce;

class page_store_reports_itemcustomfieldstock extends \xepan\commerce\page_store_reports_storereportsidebar{
	public $title = "Item Stock According to Custom Field";

	function init(){
		parent::init();
		
		$this->app->stickyGET('selected_item');

		$item_model = $this->add('xepan\commerce\Model_Item_Stock');
		$item_model->setLimit(20);

		$form = $this->add('Form');
		$item_field = $form->addField('DropDown','item')->validate('required');
		$item_field->setModel($item_model);
		$item_field->setEmptyText("please select an item");

		$form->addSubmit('Submit');

		$view = $this->add('xepan\commerce\View_ItemCustomFieldStock');
		if($_GET['selected_item']){
			$item_model->addCondition('id',$_GET['selected_item']);
		}

		$view->setModel($item_model,['name','opening','purchase','purchase_return','consumed','consumption_booked','received','adjustment_add','adjustment_removed','issue','issue_submitted','sales_return','movement_in','movement_out','shipped','delivered','net_stock']);
		
		if($form->isSubmitted()){
			$view->js()->reload(['selected_item'=>$form['item']])->execute();
		}
				
	}
}