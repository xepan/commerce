<?php

namespace xepan\commerce;

class Model_DiscountVoucher extends \Model_Table{
	public $table='discount_vouchers';
	public $status = ['Active','InActive'];
	public $actions = [
	'Active'=>['view','edit','delete','deactivate'],
	'InActive'=>['view','edit','delete','activate']
	];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Epan','epan_id');

		$this->addField('name')->caption('Voucher Number')->mandatory(true);
		$this->addField('no_user')->caption('No of Person')->defaultValue(1)->mandatory(true)->hint('Only Numeric Number');
		$this->addField('discount_amount')->type('money')->caption('Discount Amount %')->type('int')->mandatory(true)->hint('Discount Amount in %');
		$this->addField('start_date')->caption('Strating Date')->type('date')->defaultValue(date('Y-m-d'))->mandatory(true);
		$this->addField('exp_date')->caption('Expire Date')->type('date');

		$this->addField('created_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);
		$this->addField('updated_at')->type('date')->defaultValue($this->app->now)->sortable(true)->system(true);

		$this->addField('search_string')->type('text')->system(true)->defaultValue(null);

		$this->hasOne('xepan\base\Contact','created_by_id')->defaultValue($this->app->epan->id)->system(true);
		$this->hasOne('xepan\base\Contact','updated_by_id')->defaultValue($this->app->epan->id)->system(true);
		
		$this->hasMany('xepan/commerce/DiscountVoucherUsed','discountvouchers_id');
		$this->addHook('beforeDelete',$this);

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
		$discountvoucherused->save();
	}


 //  	function isVoucherExpired(){

	// 	$current_date= $this->app->now;
	// 	if( strtotime($current_date) > strtotime($this['exp_date']))
	// 		return true;
	// 	else
	// 		return false;
	// }

	// function isVoucherUsable($voucher_no){

	// 	$voucher=$this->add('xepan/commerce/Model_DiscountVoucher');
	// 	$voucher->addCondition('name',$voucher_no);
	// 	var_dump($voucher_no) ;
	// 	$voucher->tryLoadAny();
	// 	if(!$voucher->loaded()){
	// 		return false;
	// 	}
			
	// 	// if voucher expired then give error message
	// 	if($voucher->isExpire()){
	// 		return false;
	// 	}
	// 	// if voucher is not expired, how many used it 
	// 	else{
	// 		$person_used=$voucher->ref('xepan/commerce/DiscountVoucherUsed')->count()->getOne();					
	// 		if($voucher['no_user'] > $person_used){
	// 			return $voucher['discount_amount'];
	// 		}
	// 		// if no of allowed person already consumed it then, error message 
	// 		else{
	// 			return false;
	// 		}
							
	// 	}
	// }
}

