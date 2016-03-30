<?php
namespace xepan\commerce;

class Tool_ItemImage extends \xepan\cms\View_Tool{
	public $option = [
	];


	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];		
		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
		$image = $item->ref('ItemImages');
		$image->tryLoadAny();		


		$lister = $this->add('CompleteLister',null,null,['view/tool/itemimage']);
		$lister->setModel($image);

		$lister->template->set('firstimage',$this->add('xepan\commerce\Model_Item')->load($item_id)->ref('ItemImages')->setLimit(1)->fieldQuery('file'));
		

		// throw new \Exception($image);
		
	}

	function render(){

		$this->js(true)->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery-elevatezoom.js')
					   ->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery.fancybox.js');
		parent::render();

	}
}	