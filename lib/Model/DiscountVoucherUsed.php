<?php

namespace xepan\commerce;

class Model_DiscountVoucherUsed extends \Model_Table{
	public $table='discount_vouchers_used';

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Epan','epan_id');
		// $this->addCondition('epan_id',$this->api->current_website->id);	
		$this->hasOne('xepan/commerce/QSP_Master','qsp_master_id');
		$this->hasOne('xepan/commerce/DiscountVoucher','discountvouchers_id');
		$this->hasOne('xepan/commerce/Customer','customer_id');	

	}
}
































// function setMemberEmpty(){
// 		if(!$this->loaded()) return false;

// 		$this['member_id'] = null;
// 		$this->save();
// 	}
