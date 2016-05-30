<?php

 namespace xepan\commerce;

 class Model_Cart extends \Model{ 	

	function init(){
		parent::init();

		$this->setSource('Session');

		$this->addField('name');
		$this->addField('item_id');
		$this->addField('item_code');
		$this->addField('item_member_design_id');
		$this->addField('unit_price')->type('money');
		$this->addField('qty');
		$this->addField('original_amount')->type('money');
		$this->addField('sales_amount')->type('money');
		$this->addField('shipping_charge')->type('money');
		$this->addField('shipping_duration')->type('text');
		$this->addField('shipping_duration_days')->type('Number');
		$this->addField('express_shipping_charge')->type('text');
		$this->addField('express_shipping_duration')->type('text');
		$this->addField('express_shipping_duration_days')->type('Number');
		$this->addField('tax_percentage');
		$this->addField('taxation_id');
		$this->addField('file_upload_ids'); // array of uploaded file/image  file store id
		$this->addField('custom_fields')->type('text');
	}

	function addItem($item_id,$qty,$item_member_design_id=null, $custom_fields=null,$file_upload_id_array=[]){
		$this->unload();

		if(!is_numeric($qty)) $qty=1;

		if(!is_numeric($item_id)) return;

		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
		
		$amount_array = $item->getAmount($custom_fields,$qty,'retailer');

		$this['item_id'] = $item->id;
		$this['item_code'] = $item['sku'];
		$this['name'] = $item['name'];
		$this['unit_price'] = ($amount_array['sale_amount'] / $qty);
		$this['qty'] = $qty;
		$this['original_amount'] = $amount_array['original_amount'];
		$this['sales_amount'] = $amount_array['sale_amount'];
		$this['custom_fields'] = $custom_fields;
		$this['item_member_design_id'] = $item_member_design_id;
		$this['file_upload_ids'] = json_encode($file_upload_id_array);
		$this['shipping_charge'] = $amount_array['shipping_charge'];
		$this['express_shipping_charge'] = $amount_array['express_shipping_charge'];
		$this['express_shipping_duration'] = $amount_array['express_shipping_duration'];
		$this['shipping_duration'] = $amount_array['shipping_duration'];
		$this['shipping_duration_days'] = $amount_array['shipping_duration_days'];
		$this['express_shipping_duration_days'] = $amount_array['express_shipping_duration_days'];
		$this['taxation_id'] = @$amount_array['taxation']->id;
		$this->save();
	}

	function getItemCount(){
		return $this->count()?:0;
	}

	function getImageUrl(){
		if(!$this->loaded())
			throw new \Exception("model must loaded, cart");
		
		$img_model=$this->add('xepan\commerce\Model_Item_Image');
		$img_model->addCondition('item',$this['item_id']);
		$img_model->tryLoadAny()->setLimit(1);
		$img_url = $img_model['thumb_url']?:"logo.svg";
		
		return $img_url;
	}

	//Return 
	function getItemQtyCount(){
		
		$item_count=0;
		foreach ($this as $junk) {
			$item_count += $junk['qty'];
		}
		return $item_count;
	}

	function getNetAmount() { 
		$total_amount=0;
		$cart=$this->add('xepan\commerce\Model_Cart');
		$sum = 0;
		foreach ($cart as $junk) {
			$total_amount = (float)$total_amount + (float)$junk['total_amount'];

		}
		
		return $total_amount;
	}

	function getTotalDiscount($percentage=false){
		$discount = 0;
		$total_amount=0;
		$original_total_amount = 0;
		$cart=$this->add('xepan\commerce\Model_Cart');
		// $carts = "";
		foreach ($cart as $junk) {
			if($junk['original_amount']){
				$total_amount += $junk['total_amount'];
				$original_total_amount += ($junk['original_amount'] + $this['shipping_charge'] + $this['tax']);
			}
		}

		return 0;//$total_amount - $original_total_amount;

	}


	function emptyCart(){
		 foreach ($this as $junk) {
			$this->delete();
		 }
	}

	function updateCart($cart_id, $qty){
		
		if(!$this->loaded()){
			$this->add('xepan\commerce\Model_Cart')->load($cart_id);
		}

		if(!is_numeric($qty)) $qty=1;

		$item = $this->add('xepan\commerce\Model_Item')->load($this['item_id']);
		$amount_array = $item->getAmount($this['custom_fields'],$qty,'retailer');
		
		$this['item_id'] = $item->id;
		$this['item_code'] = $item['sku'];
		$this['name'] = $item['name'];
		$this['unit_price'] = ( $amount_array['sale_amount'] / $qty );
		$this['qty'] = $qty;
		$this['original_amount'] = $amount_array['original_amount'];
		$this['sales_amount'] = $amount_array['sale_amount'];
		$this['shipping_charge'] = $amount_array['shipping_charge'];
		$this['shipping_duration'] = $amount_array['shipping_duration'];
		$this['express_shipping_duration'] = $amount_array['express_shipping_duration'];
		$this['express_shipping_charge'] = $amount_array['express_shipping_charge'];
		$this['shipping_duration_days'] = $amount_array['shipping_duration_days'];
		$this['express_shipping_duration_days'] = $amount_array['express_shipping_duration_days'];
		
		$this['taxation_id'] = $amount_array['taxation']->id;
		$this->save();
	}

	function deleteItem($cartitem_id){
		$this->load($cartitem_id);
		$this->delete();
	}
}
 