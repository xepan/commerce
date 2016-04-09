<?php

namespace xepan\commerce;

class Tool_Detail extends \xepan\cms\View_Tool{
	public $options = [
				// 'display_layout'=>'item-description',/*flat*/
					 
				];

	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];		
		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);

		$this->template->set('name',$item['name']);
		$this->template->set('description',$item['description']);

		$spf = $this->add('xepan\commerce\Model_Item_CustomField_Association')
		->addCondition('item_id',$item->id);
		$grid_s = $this->add('xepan\base\Grid',null,'specification',['view/tool/specification']);
		$grid_s->setModel($spf);

		$options = ['button_name'=>$this->options['addtocart_name']];
		
		$cart_btn = $this->add('xepan\commerce\Tool_Item_AddToCartButton',
					['options'=>$options],
					'Addtocart');
		$cart_btn->setModel($item);
		$this->template->set['Addtocart'] = $cart_btn->getHtml();

	}

	function defaultTemplate(){
		return ['view/tool/'.$this->options['layout']];
	}
	
}