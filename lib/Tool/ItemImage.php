<?php
namespace xepan\commerce;

class Tool_ItemImage extends \xepan\base\View_Tool{
	public $option = [
	];


	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];
		$item = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
		if(!$item->loaded())
			throw $this->exception('Item not found');

		
	}

	function defaultTemplate(){
		return ['view/tool/itemimage'];
	}
}	