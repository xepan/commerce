<?php

namespace xepan\commerce;

class Model_DiscountVoucher extends \xepan\base\Model_Table{
	public $table='discount_voucher';
	public $status = ['Active','DeActive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate','condition','used'],
					'DeActive'=>['view','edit','delete','activate']
				];

	function init(){
		parent::init();
		
		// $this->hasOne('xepan\base\Epan','epan_id');
		// $this->addCondition('epan_id',$this->app->epan->id);

		$this->hasOne('xepan\base\Contact','created_by_id')->defaultValue($this->app->employee->id)->system(true);
		$this->hasOne('xepan\base\Contact','updated_by_id')->defaultValue($this->app->employee->id)->system(true);
		$this->hasOne('xepan\commerce\Category','on_category_id');

		$this->addField('name')->caption('Voucher Number');
		$this->addField('start_date')->caption('Starting Date')->type('date')->defaultValue(date('Y-m-d'))->mandatory(true);
		$this->addField('expire_date')->type('date');
		$this->addField('no_of_person')->type('Number')->defaultValue(1)->hint('How many person ? (i.e. customer for online purchasing)');
		$this->addField('one_user_how_many_time')->type('Number')->defaultValue(1)->hint('How many time it can be used by one customer during online purchasing ?');
		$this->addField('on')->setValueList(['price'=>"Price",'shipping'=>"Shipping",'gross'=>'Gross (Both)'])->defaultValue('price');
		$this->addField('include_sub_category')->type('boolean');
		$this->addField('based_on')->setValueList([
							// 'weight'=>'Weight',
							// 'quantity'=>'Quantity',
							// 'Amount'=>"Amount",
							'gross_amount'=>"Gross Amount"
							]);

		$this->addField('created_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('updated_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);

		$this->hasMany('xepan/commerce/DiscountVoucherCondition','discountvoucher_id');
		$this->hasMany('xepan/commerce/DiscountVoucherUsed','discountvoucher_id');

		$this->addField('type');
		$this->addField('status')->enum(['Active','DeActive'])->defaultValue('Active');
		$this->addCondition('type','Discount_Voucher');
		
		$this->is([
				'name|to_trim|required|unique_in_epan',
				'start_date|required',
				'expire_date|required',
				'no_of_person|number|>0',
				'one_user_how_many_time|number|>0',
				'on|required'
				]);

		$this->addHook('beforeDelete',$this);	
	}

	//activate Voucher
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Voucher : '".$this['name']."' now active", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_discountvoucher")
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	//deactivate Voucher
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Voucher : '". $this['name'] ."' has been deactivated", null /*Related Document ID*/,null /*Related Contact ID*/,null,null,"xepan_commerce_discountvoucher")
            ->notifyWhoCan('deactivate','Active',$this);
		return $this->save();
	}

	function beforeDelete($m){	
		if($m->ref('xepan/commerce/DiscountVoucherUsed')->count()->getOne())
			throw new \Exception("First Delete the Related Orders/Invoices");
  	}

  	function discountVoucherUsed($discount_voucher){

		$this->addCondition('name',$discount_voucher);
		$this->tryLoadAny();
		$discountvoucherused=$this->add('xepan/commerce/Model_DiscountVoucherUsed');
		$discountvoucherused['contact_id']=$this->app->auth->model->id;
		$discountvoucherused['discountvoucher_id']=$this['id'];
		$discountvoucherused['qsp_master_id']=$this[''];
		$discountvoucherused->addHook('afterSave',function($m){
			$this->app->employee
					->addActivity("Discount Voucher : '".$this['name']."' Used By Customer : '".$m['contact']."' On Sales Order No :'".$m['qsp_master_id']."'", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_discountvoucher")
					->notifyWhoCan('used','Active');
		});
		$discountvoucherused->save();
	}


  	function isVoucherExpired(){
		$current_date = $this->app->now;
		if( strtotime($current_date) > strtotime($this['expire_date']))
			return true;
		else
			return false;
	}

	function isVoucherUsable($voucher_no){
		if(! trim($voucher_no))
			return "no voucher found";

		// $voucher=$this->add('xepan/commerce/Model_DiscountVoucher');
		$this->addCondition('name',$voucher_no);
		$this->tryLoadAny();
		if(!$this->loaded()){
			return "coupon not found";
		}
		// if voucher expired then give error message
		if($this->isVoucherExpired()){
			return "coupon expired";
		}else{
		 	// if voucher is not expired, how many used it
			$person_used = $this->ref('xepan/commerce/DiscountVoucherUsed')->count()->getOne();
			if($this['no_of_person'] > $person_used){
				return "success";
			}
			// if no of allowed person already consumed it then, error message 
			else{
				return "coupon limit number of person exit";
			}
							
		}
	}

	function page_condition($page){
		if(!$this->loaded())
			throw new \Exception("model discount voucher must loaded");
		
		$condition_model = $page->add('xepan\commerce\Model_DiscountVoucherCondition');
	    $condition_model->addCondition('discountvoucher_id',$this->id);
	    $crud = $page->add('xepan\hr\CRUD',null,null,['view/discount/vouchers/condition-grid']);
	    $crud->setModel($condition_model);
	}
	
	function page_used($page){
		if(!$this->loaded())
			throw new \Exception("model discount voucher must loaded");

	    $used = $page->add('xepan\commerce\Model_DiscountVoucherUsed');
	    $used->addCondition('discountvoucher_id',$this->id);
	    $crud = $page->add('xepan\hr\CRUD');
	    $crud->setModel($used);
	}

	//return amount
	function getDiscountAmount($CartActualTotalExcludingItem, $itemActualTotal, $itemActualShipping,$itemActualShippingExpress){
		
		$discount_amount = ['on_price'=>0,'on_shipping'=>0,'on_shipping_express'=>0];
		
		$cartActualGrossTotal = $CartActualTotalExcludingItem + $itemActualTotal;
		
		if(!$this->loaded()){
			return $discount_amount;
		}

		// if discount amount on price then total amount
		$on_price=false;
		$on_shipping = false;
		if($this['on'] === "price"){
			$on_price = true;
		}
		
		// if discpount amount on shipping then total shipping
		if($this['on'] === "shipping"){
			$on_shipping = true;
		}

		// if discpount amount on shipping then total shipping
		if($this['on'] === "gross"){
			$on_price=true;
			$on_shipping = true;
		}


		$voucher_condition = $this->add('xepan\commerce\Model_DiscountVoucherCondition')->addCondition('discountvoucher_id',$this->id);
				$voucher_condition->addCondition('from',"<=",(int)$cartActualGrossTotal);
				$voucher_condition->addCondition('to',">=",(int)$cartActualGrossTotal);
				$voucher_condition->tryLoadany();
				if($voucher_condition->loaded()){
					$discount_array = explode("%",$voucher_condition['name']);
					$discount_percentage = trim($discount_array[0]);
					if(!isset($discount_array[1])){ // no % at last
						if($on_price)
							$discount_amount['on_price'] = $itemActualTotal / $cartActualGrossTotal * $discount_percentage;
						if($on_shipping){
							$discount_amount['on_shipping'] = $itemActualTotal / $cartActualGrossTotal * $discount_percentage;
							$discount_amount['on_shipping_express'] = $discount_amount['on_shipping'];
						}
					}else{ // % value
						if($on_price)
							$discount_amount['on_price'] = $itemActualTotal * $discount_percentage / 100.00;
						if($on_shipping){
							$discount_amount['on_shipping'] = $itemActualShipping * $discount_percentage / 100.00;
							$discount_amount['on_shipping_express'] = $itemActualShippingExpress * $discount_percentage / 100.00;
						}
					}
				}

		return $discount_amount;
	}

}

