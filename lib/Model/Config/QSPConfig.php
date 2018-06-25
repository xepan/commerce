<?php

namespace xepan\commerce;

class Model_Config_QSPConfig extends \xepan\base\Model_ConfigJsonModel{
	public $fields = [
					'discount_per_item'=>'checkbox',
					'discount_on_taxed_amount'=>'checkbox',
					'tax_on_discounted_amount'=>'checkbox',
					'quotation_serial'=>'line',
					'sale_order_serial'=>'line',
					'sale_invoice_serial'=>'line',
					'show_shipping_address_in_pos'=>'checkbox',

				];
	public $config_key = 'COMMERCE_QSP_TAX_AND_DISCOUNT_CONFIG';
	public $application='commerce';

	function init(){
		parent::init();

		$this->getElement('show_shipping_address_in_pos')->defaultValue(1);
	}
}