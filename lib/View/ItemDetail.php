<?php

namespace xepan\commerce;

class View_ItemDetail extends \View{
	
	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];
		if(!$item_id)
			return;
		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
				
		$this->setModel($item);
	}

	function defaultTemplate(){
		return ['view/item/itemdetail'];
	
	}
}