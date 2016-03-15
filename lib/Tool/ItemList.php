<?php

namespace xepan\commerce;

class Tool_ItemList extends \xepan\base\View_Tool{
	public $options = [

					'show_name'=>true,
					'show_sku'=>true,/* true, false*/
					'sku'=>"Not Available",
					'show_sale_price'=>true,/* true, false*/
					'show_original_price'=>true,/* true, false*/
					'show_description'=>true,/* true, false*/ 
					'description'=>"Not Available",
					'show_tags'=>true,/* true, false*/ 
					'tags'=>"Not Tag Yet"
				];

	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];
		$item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
		if(!$item->loaded())
			throw $this->exception('Item not found');
		$this->setModel($item)->tryLoadAny($item_id);

	}

	function defaultTemplate(){
		return ['view/tool/itemview'];
	}
}