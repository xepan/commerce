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
		// $this->addField('original_amount')->type('money');
		// $this->addField('sales_amount')->type('money');
		$this->addField('shipping_charge')->type('money');
		$this->addField('tax_percentage');
		$this->addField('file_upload_id');
		$this->addField('custom_fields')->type('text');		

		$this->addHook('afterLoad',function($m){
			$m['amount_excluding_tax']=$m['unit_price'] * $m['qty'];
			$m['tax_amount']=$m['amount_excluding_tax']*$m['tax_percentage']/100.00;
			$m['amount_including_tax']=$m['amount_excluding_tax']+$m['tax_amount']+$m['shipping_charge'];
		});

	}

	function addItem($item_id,$qty,$item_member_design_id=null, $custom_fields=null,$shipping_charge=0,$file_upload_id_array=[]){
		$this->unload();

		if(!is_numeric($qty)) $qty=1;

		if(!is_numeric($item_id)) return;

		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);
		$prices = $item->getPrice($custom_fields,$qty,'retailer');
		
		$amount = $item->getAmount($custom_fields,$qty,'retailer');
		
		$tax_percentag = 5;
		$tax = round( ( $amount['sale_amount']* $tax_percentag)/100 , 2);

		$total = round( ($amount['sale_amount'] + $tax),2);
		$total = $total + $this['shipping_charge'];
		
		$this['item_id'] = $item->id;
		$this['item_code'] = $item['sku'];
		$this['name'] = $item['name'];
		$this['unit_price'] = $prices['sale_price'];
		$this['qty'] = $qty;
		$this['original_amount'] = $amount['original_amount'];
		$this['sales_amount'] = $total;
		$this['custom_fields'] = $custom_fields;
		$this['item_member_design_id'] = $item_member_design_id;
		$this['file_upload_id'] = json_encode($file_upload_id_array);
		$this['shipping_charge'] = $shipping_charge;
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

	function updateCart($id, $qty){
		
		if(!$this->loaded())
			throw new \Exception("Cart Model Not Loaded at update cart".$this['item_name']);
		
		if(!is_numeric($qty)) $qty=1;

		$item = $this->add('xepan\commerce\Model_Item')->load($this['item_id']);
		$prices = $item->getPrice($this['custom_fields'],$qty,'retailer');
		
		$amount = $item->getAmount($this['custom_fields'],$qty,'retailer');
		$tax_percentag = 5;
		$tax = round( ( $amount['sale_amount']* $tax_percentag)/100 , 2);

		$total = round( ($amount['sale_amount'] + $tax),2);
		
		$this['rateperitem'] = $prices['sale_price'];
		$this['qty'] = $qty;
		$this['original_amount'] = $amount['original_amount'];
		$this['sales_amount'] = $total;
		$this['total_amount'] = $total;
		$this['tax'] = $tax;		
		$this['tax_percentage'] = $tax_percentag;
		$this['shipping_charge'] = "todo";
		$this->save();
	}

	function deleteItem($cartitem_id){
		$this->load($cartitem_id);
		$this->delete();
	}

}
 