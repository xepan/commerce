<?php

namespace xepan\commerce;

class Tool_Detail extends \xepan\cms\View_Tool{
	public $options = [
				// 'display_layout'=>'item-description',/*flat*/
				];

	function init(){
		parent::init();

		// $item_id = $_GET['commerce_item_id'];
		$item = $this->add('xepan\commerce\Model_Item');
			///throw $this->exception('Item not found');
		$this->setModel($item)->tryLoadAny();

		// $options = [
		// 			'button_name'=>$this->options['addtocart_name']
		// 			];


		// $cart = $this->add('xepan\commerce\Tool_Item_AddToCartButton',
		// 	[
		// 		'name' => "addtocart_view_".$l->model->id,
		// 		'options'=>$options
		// 	],
		// 	''
		// 	);
	}

	function defaultTemplate(){
		return ['view/tool/'.$this->options['layout']];
	}

}