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
		$this->add('misc/Field_Callback','amount_excluding_tax')->set(function($m){
			return round($m['unit_price'] * $m['qty'], 2);
		});
		$this->addField('tax_percentage');
		$this->addField('shipping_charge');

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
		$this['tax_percantage'] = $data['tax_percantage'];
		$this['amount_excluding_tax'] = $data['amount_excluding_tax'];
		$this['total_amount'] = $data['total_amount'];
		$this['tax_amount'] = $data['tax_amount'];

		$this->save();

	}

}
 