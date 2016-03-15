<?php

namespace xepan\commerce;

class Tool_ItemDetail extends \xepan\base\View_Tool{
	public $options = [
					'show_sku'=>true,/* true, false*/ 
					'is_saleable'=>'1',
					'sku'=>"HELLOoooo",
					'show_display_sequence'=>true/* true, false*/ 
				];

	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];
		$item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
		if(!$item->loaded())
			throw $this->exception('Item not found');
		$this->setModel($item)->tryLoad($item_id);

	}

	function defaultTemplate(){
		return ['view/item/itemdetail'];
	}
}