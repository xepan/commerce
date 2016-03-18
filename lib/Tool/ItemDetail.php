<?php

namespace xepan\commerce;

class Tool_ItemDetail extends \xepan\cms\View_Tool{
	public $options = [
				'display_layout'=>'tabs',/*flat*/
				];

	function init(){
		parent::init();

		// $item_id = $_GET['commerce_item_id'];
		$item = $this->add('xepan\commerce\Model_Item');
			///throw $this->exception('Item not found');
		$this->setModel($item)->tryLoadAny();

	}

	function defaultTemplate(){
		return ['view/tool/itemdetail_view2'];
	}
}