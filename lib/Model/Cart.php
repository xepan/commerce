<?php

 namespace xepan\commerce;

 class Model_Cart extends \Model{ 	

 	public $discount_voucher = null;
 	public $is_express_shipping=false;

	function init(){
		parent::init();

		$this->setSource('Session');

		$this->addField('name');
		$this->addField('item_member_design_id');
		
		$this->addField('item_id');
		$this->addField('item_code');

		$this->addField('tax_percentage');
		$this->addField('taxation_id');
		
		$this->addField('raw_sale_price');
		$this->addField('raw_original_price');

		$this->addField('unit_price')->type('money');
		$this->addField('qty');

		$this->addField('raw_amount');
		$this->addField('row_discount');
		$this->addField('discounted_raw_amount');
		
		$this->addField('raw_shipping_charge')->type('money');
		$this->addField('row_discount_shipping');
		$this->addField('raw_express_shipping_charge')->type('money');
		$this->addField('row_discount_shipping_express');
		
		$this->addField('discounted_raw_shipping');
		$this->addField('discounted_raw_shipping_express');

		$this->addField('tax_amount');

		$this->addField('amount'); // taxed
		$this->addField('shipping_charge')->type('money'); // taxed
		$this->addField('express_shipping_charge')->type('money'); // taxed
		


		$this->addField('original_amount')->type('money');
		$this->addField('sales_amount')->type('money');

		
		$this->addField('shipping_duration')->type('text');
		$this->addField('shipping_duration_days')->type('Number');
		$this->addField('express_shipping_duration')->type('text');
		$this->addField('express_shipping_duration_days')->type('Number');
		
		$this->addField('file_upload_ids'); // array of uploaded file/image  file store id
		$this->addField('custom_fields')->type('text');

		$this->addField('qty_unit_id');

		$this->discount_voucher = $this->app->recall('discount_voucher_obj',$this->add('xepan\commerce\Model_DiscountVoucher'));
		$this->is_express_shipping = $this->app->recall('express_shipping',false);
	}

	function addItem($item_id,$qty,$item_member_design_id=null, $custom_fields=null,$file_upload_id_array=[],$skip_unload=false){
		
		if(!$skip_unload)
			$this->unload();


		if(!is_numeric($qty)) $qty=1;

		if(!is_numeric($item_id)) return;

		$item = $this->add('xepan\commerce\Model_Item')->load($item_id);

		$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'tax_on_shipping'=>'checkbox'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$misc_config->tryLoadAny();

		$tax_on_shipping = $misc_config['tax_on_shipping'];
		
		$amount_array = $item->getAmount($custom_fields,$qty,'retailer');
		
		$discounts = $this->discount_voucher->
				getDiscountAmount(
					$this->getActualTotal(), 
					$amount_array['raw_sale_price'] * $qty,
					$amount_array['raw_shipping_charge'],
					$amount_array['raw_express_shipping_charge']
				);
		
		// echo "<pre>";
		// print_r($amount_array);
		// print_r($discounts);
		// echo "</pre>";

		$taxper= $this['tax_percentage'] = $amount_array['taxation']?$amount_array['taxation']['percentage']:0;
		$this['taxation_id'] = @$amount_array['taxation']['taxation_id'];
		
		$this['item_id'] = $item->id;
		$this['item_code'] = $item['sku'];
		$this['name'] = $item['name'];
		$this['raw_sale_price'] = $amount_array['raw_sale_price'];
		$this['qty'] = $qty;
		
		$this['raw_amount'] = $this['raw_sale_price']*$this['qty'];

		$this['raw_shipping_charge'] = $amount_array['raw_shipping_charge'];
		$this['raw_express_shipping_charge'] = $amount_array['raw_express_shipping_charge'];

		$this['row_discount'] = $discounts['on_price'];
		$this['row_discount_shipping'] = $discounts['on_shipping'];
		$this['row_discount_shipping_express'] = $discounts['on_shipping_express'];
		
		$this['discounted_raw_amount'] = $this['raw_amount'] - $this['row_discount'];

		$this['tax_amount'] = $this['discounted_raw_amount'] * $taxper/100.00;
		// $this['unit_price'] = round(($this['discounted_raw_amount'] +  $this['tax_amount']) / $this['qty'],2);
		$this['unit_price'] = $this['raw_sale_price'] + ($this['raw_sale_price']*$taxper/100);

		$this['amount'] = round($this['unit_price'] * $this['qty'],2);
		// $this['amount'] = $this['discounted_raw_amount']+ $this['tax_amount'];

		// echo $this['unit_price']."<br/>";
		// echo $this['amount']."<br/>";
		$this['qty_unit_id'] = $item['qty_unit_id'];
		
		$shipping_tax_amount =0;
		$shipping_tax_amount_express =0;

		if($tax_on_shipping){
			if($this->is_express_shipping){
				$shipping_tax_amount_express =  (($this['raw_express_shipping_charge'] - $this['row_discount_shipping_express']) * $taxper /100.00);
				$this['tax_amount'] += $shipping_tax_amount_express;
			}
			else{
				$shipping_tax_amount =  (($this['raw_shipping_charge'] - $this['row_discount_shipping']) * $taxper /100.00);
				$this['tax_amount'] += $shipping_tax_amount;
			}
		}

		$this['shipping_charge'] = $this['raw_shipping_charge'] - $this['row_discount_shipping'] + $shipping_tax_amount;
		$this['express_shipping_charge'] = $this['raw_express_shipping_charge'] - $this['row_discount_shipping_express'] + $shipping_tax_amount_express;

		$this['sales_amount'] = $this['amount'] + $this['shipping_charge'];
		$this['custom_fields'] = $custom_fields;
		$this['item_member_design_id'] = $item_member_design_id;
		$this['file_upload_ids'] = json_encode($file_upload_id_array);
		$this['express_shipping_duration'] = $amount_array['express_shipping_duration'];
		$this['shipping_duration'] = $amount_array['shipping_duration'];
		$this['shipping_duration_days'] = $amount_array['shipping_duration_days'];
		$this['express_shipping_duration_days'] = $amount_array['express_shipping_duration_days'];

		$this['raw_original_price'] = $amount_array['raw_original_price'];

		$this->save();
	}

	function getItemCount(){
		return $this->count()?:0;
	}

	function getImageUrl(){
		if(!$this->loaded())
			throw new \Exception("model must loaded, cart");
		
		$img_model=$this->add('xepan\commerce\Model_Item_Image');
		$img_model->addCondition('item_id',$this['item_id']);
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

	function getActualTotal($leave_item_id=null){
		$total=0;
		$cart=$this->add('xepan\commerce\Model_Cart');
		foreach ($cart as $junk) {
			if($junk['id']===$leave_item_id) continue;
			$total += ($junk['raw_sale_price'] * $junk['qty']) ;
		}
	}

	function getTotals(){
		$totals = [
					'raw_sale_price' => 0,
					'raw_original_price' => 0,
					'tax_percentage' => 0,
					'unit_price' => 0,
					'qty'=>0,
					'raw_amount' => 0,
					'row_discount' => 0,
					'discounted_raw_amount' => 0,
					'raw_shipping_charge' => 0,
					'row_discount_shipping' => 0,
					'raw_express_shipping_charge' => 0,
					'row_discount_shipping_express' => 0,
					'discounted_raw_shipping' => 0,
					'discounted_raw_shipping_express' => 0,
					'tax_amount' => 0,
					'amount' => 0,
					'shipping_charge' => 0,
					'express_shipping_charge' => 0,
					'original_amount' => 0,		
					'sales_amount' => 0,
					'total_item_count' => 0
				];

		$cart = $this->add('xepan\commerce\Model_Cart');
		foreach ($cart as $item) {
			$totals['raw_sale_price'] += $item['raw_sale_price'];
			$totals['raw_original_price'] += $item['raw_original_price'];
			$totals['tax_percentage'] += $item['tax_percentage'];
			$totals['qty'] += $item['qty'];
			$totals['raw_amount'] += $item['raw_amount'];
			$totals['row_discount'] += $item['row_discount'];
			$totals['discounted_raw_amount'] += $item['discounted_raw_amount'];
			$totals['raw_shipping_charge'] += $item['raw_shipping_charge'];
			$totals['row_discount_shipping'] += $item['row_discount_shipping'];
			$totals['raw_express_shipping_charge'] += $item['raw_express_shipping_charge'];
			$totals['row_discount_shipping_express'] += $item['row_discount_shipping_express'];
			$totals['discounted_raw_shipping'] += $item['discounted_raw_shipping'];
			$totals['discounted_raw_shipping_express'] += $item['discounted_raw_shipping_express'];
			$totals['tax_amount'] += $item['tax_amount'];
			$totals['amount'] += $item['amount'];
			$totals['shipping_charge'] += $item['shipping_charge'];
			$totals['express_shipping_charge'] += $item['express_shipping_charge'];
			$totals['original_amount'] += $item['original_amount'];
			$totals['sales_amount'] += $item['sales_amount'];
			$totals['total_item_count'] += 1;
		}

		return $totals;
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
		$this->app->forget('discount_voucher');
		$this->app->forget('discount_voucher_obj');
		$this->app->forget('express_shipping');
		 foreach ($this as $junk) {
			$this->delete();
		 }

	}

	function updateCart($cart_id, $qty){
		
		if(!is_numeric($qty)) $qty=1;

		$old_item = $this->add('xepan\commerce\Model_Cart')->load($cart_id);
		
		$item_id = $old_item['item_id'];
		$item_member_design_id = $old_item['item_member_design_id'];
		$custom_fields = $old_item['custom_fields'];
		$file_upload_id_array = $old_item['file_upload_id_array'];

		$this->load($cart_id);
		$this->addItem($item_id,$qty,$item_member_design_id,$custom_fields,$file_upload_id_array,$skip_unload=true);
	}

	function reloadCart(){
		foreach ($this as $junk) {
			$this->updateCart($this->id,$this['qty']);
		 }
	}

	function deleteItem($cartitem_id){
		$this->load($cartitem_id);
		$this->delete();
	}

	function hasItemMemberDssignId(){

		foreach ($this as $model) {
			if($model['item_member_design_id'])
				return true;
		}

		return false;
	}
}
 