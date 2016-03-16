<?php

namespace xepan\commerce;

class Tool_ItemDetail extends \xepan\base\View_Tool{
	public $options = [
				'display_layout'=>'tabs'/*flat*/
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