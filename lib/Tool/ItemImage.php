<?php
namespace xepan\commerce;

class Tool_ItemImage extends \xepan\cms\View_Tool{
	public $option = [
	];


	function init(){
		parent::init();

		$item_id = $_GET['commerce_item_id'];		
		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
		$image = $item->ref('Attachments');
		// $image->addExpression('thumb_url')->set(function($m,$q){
		// $file = $this->add('filestore\Field_Image')->load($m['file_id']);

		// 	return $file->;
		// });

		$lister = $this->add('CompleteLister',null,null,['view/tool/itemimage']);
		$lister->setModel($image);

		//For set First Image
		$lister->template->set('firstimage',$this->add('xepan\commerce\Model_Item')->load($item_id)->ref('Attachments')->setLimit(1)->fieldQuery('file'));
		
	}

	function render(){

		$this->js(true)->_load('tool/jquery-elevatezoom')
					   ->_load('tool/jquery.fancybox');
		parent::render();

	}
}	