<?php

namespace xepan\commerce;

class Model_DiscountVoucherCondition extends \xepan\base\Model_Table{
	public $table='discount_voucher_condition';

	function init(){
		parent::init();
		
		$this->hasOne('xepan/commerce/DiscountVoucher','discountvoucher_id');

		$this->addField('from');
		$this->addField('to');
		$this->addField('name')->caption('Discount Amount \ Percentage');

		$this->addField('type');
		$this->addCondition('type','Discount_Voucher_Condition');

		$this->is([
					'discountvoucher_id|required|number|>0',
					'from|required',
					'to|required',
					'name|required'
				]);
	}
}