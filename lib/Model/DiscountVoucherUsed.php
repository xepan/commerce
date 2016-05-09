<?php

namespace xepan\commerce;

class Model_DiscountVoucherUsed extends \Model_Table{
	public $table='discount_voucher_used';

	function init(){
		parent::init();
		
		$this->hasOne('xepan/commerce/QSP_Master','qsp_master_id');
		$this->hasOne('xepan/commerce/DiscountVoucher','discountvoucher_id');
		$this->hasOne('xepan/commerce/Customer','customer_id');	

	}
}
































// function setMemberEmpty(){
// 		if(!$this->loaded()) return false;

// 		$this['member_id'] = null;
// 		$this->save();
// 	}
