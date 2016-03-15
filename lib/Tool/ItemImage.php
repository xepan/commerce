<?php
namespace xepan\commerce;

class Tool_ItemImage extends \xepan\base\View_Tool{
	public $option = [
	];


	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];		
		$image = $this->add('xepan\commerce\Model_Item')->load($item_id)->ref('Attachments');

		$this->add('Lister',null,null,['view/tool/itemimage'])->setModel($image);
	}
}	