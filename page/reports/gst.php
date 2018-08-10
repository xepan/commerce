<?php

namespace xepan\commerce;

class page_reports_gst extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'GST Report';

	function init(){
		parent::init();
		
		$grid = $this->add('xepan\hr\Grid');
		$m = $this->add('xepan\commerce\Model_QSP_GstDetail');
		$m->addCondition('qsp_type','SalesInvoice');
		$grid->setModel($m,['created_at','invoice_number','customer','customer_gstin','state_place_of_supply','product_type','description','hsn_sac','quantity','qty_unit','price','discount','tax_percentage','cgst_rate','cgst_amount','sgst_rate','sgst_amount','igst_rate','igst_amount','cess_rate','cess_amount','total_transaction_value','is_this_is_a_bill_of_supply','is_this_is_a_nil_rate_exempt_non_gst_item','is_reverse_charge_applicable','type_of_export','shipping_port_code_export','shipping_bill_number_export','shipping_bill_date_export','has_gst_idt_tds_been_deducted','my_gstin','customer_billing_address','customer_billing_city','customer_billing_state','is_this_document_cancelled','is_customer_composition_dealer','return_filling_period','gstin_of_ecommerce_marketplace','date_of_linked_advance_receipt','voucher_number_of_linked_receipt','adjustment_amount_of_linked_receipt'
	]);

		$grid->addFormatter('description','wrap');
		$grid->add('misc\Export');
}
}