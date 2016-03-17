<?php 
 namespace xepan\commerce;
 class page_test extends \Page{

	public $title='Test';

	function init(){
		parent::init();
		$item_id=$this->app->stickyGET('commerce_item_id');
		// $cart_data = [
		// 	'name'=>'Item name',
		// 	'item_id'=>2,
		// 	'unit_price'=>25.00,
		// 	'qty'=>10,
		// 	'tax_percentage'=>'14.5',
		// 	'shipping_charge'=>'100',
		// 	'type'=>'flat'
		// ];

		// $m = $this->add('xepan\commerce\Model_Cart');
		// $m->addItem($cart_data);
		$item_model=$this->add('xepan\commerce\Model_Item');
		$item_model->addCondition('id',$item_id);
		$item_model->tryLoadAny();
		 $item_details=$this->add('xepan\commerce\Tool_ItemDetail');
		 $item_details->setModel($item_model);

	}

}  
