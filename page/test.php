<?php 
 namespace xepan\commerce;
 class page_test extends \Page{

	public $title='Test';

	function init(){
		parent::init();

		$cart_data = [
			'name'=>'Item name',
			'item_id'=>2,
			'unit_price'=>25.00,
			'qty'=>10,
			'tax_percentage'=>'14.5',
			'shipping_charge'=>'100',
			'type'=>'flat'
		];

		// $m = $this->add('xepan\commerce\Model_Cart');
		// $m->addItem($cart_data);
		$this->add('xepan\commerce\Tool_Cart');


	}

}  
