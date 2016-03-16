<?php

 namespace xepan\commerce;

 class Model_Cart extends \Model{ 	

	function init(){
		parent::init();

		$this->setSource('Session');

		$this->addField('name');
		$this->addField('item_id');
		$this->addField('type');
		
		$this->addField('unit_price');
		$this->addField('qty');
		$this->addField('tax_percentage');
		$this->addField('shipping_charge');

		$this->addHook('afterLoad',function($m){
			$m['amount_excluding_tax']=number_format($m['unit_price'] * $m['qty'],2);
			$m['tax_amount']=number_format($m['amount_excluding_tax']*$m['tax_percentage']/100.00,2);
			$m['total_amount']=number_format($m['amount_excluding_tax']+$m['tax_amount']+$m['shipping_charge'],2);
		});

	}


	function addItem($data=array()){
		if(!count($data) || !is_array($data))
			throw new \Exception("array requireds");
			
		$this['name'] = $data['name'];
		$this['item_id'] = $data['item_id'];
		$this['qty'] = $data['qty'];
		$this['type'] = $data['type'];
		$this['shipping_charge'] = $data['shipping_charge'];
		$this['unit_price'] = $data['unit_price'];
		$this['tax_percentage'] = $data['tax_percentage'];
		$this['amount_excluding_tax'] = $data['amount_excluding_tax'];
		$this['total_amount'] = $data['total_amount'];
		
		$this->save();

	}

}
 