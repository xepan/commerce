<?php

namespace xepan\commerce;

class page_reports_gst extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'GST Report';
	public $taxation = [];

	function init(){
		parent::init();
		

		$grid = $this->add('xepan\hr\Grid');
		$m = $this->add('xepan\commerce\Model_QSP_GstDetail');
		$m->addCondition('qsp_type','SalesInvoice');
		$m->setLimit(10);
		$grid->setModel($m,['created_at','invoice_number','customer','customer_gstin','state_place_of_supply','product_type','description','hsn_sac','quantity','qty_unit','price','discount','tax_percentage','cgst_rate','cgst_amount','sgst_rate','sgst_amount','igst_rate','igst_amount','cess_rate','cess_amount','total_transaction_value','is_this_is_a_bill_of_supply','is_this_is_a_nil_rate_exempt_non_gst_item','is_reverse_charge_applicable','type_of_export','shipping_port_code_export','shipping_bill_number_export','shipping_bill_date_export','has_gst_idt_tds_been_deducted','my_gstin','customer_billing_address','customer_billing_city','customer_billing_state','is_this_document_cancelled','is_customer_composition_dealer','return_filling_period','gstin_of_ecommerce_marketplace','date_of_linked_advance_receipt','voucher_number_of_linked_receipt','adjustment_amount_of_linked_receipt','amount_excluding_tax']);

		foreach ($this->add('xepan\commerce\Model_Taxation') as $m) {
			$norm_name = $this->app->normalizeName($m['name']);
			$norm_name_rate = $norm_name."_rate";
			$norm_name_amount = $norm_name."_amount";

			$this->taxation[$m['id']] = [
								'percentage'=>$m['percentage'],
								'name'=>$norm_name,
								'sub_tax'=>$m['sub_tax']
							];

			$grid->addColumn($norm_name_rate);
			$grid->addColumn($norm_name_amount);
		}


		$grid->addHook('formatRow',function($g){
			$tax_id = $g->model['taxation_id'];

			if(isset($this->taxation[$tax_id])){
				$tax_array = $this->taxation[$tax_id];
				$norm_name = $tax_array['name'];
				$norm_name_rate = $norm_name."_rate";
				$norm_name_amount = $norm_name."_amount";

				$g->current_row[$norm_name_rate] = $tax_array['percentage'];
				$g->current_row[$norm_name_amount] = ($tax_array['percentage'] * $g->model['amount_excluding_tax'])/100;

				if($sub_tax = $tax_array['sub_tax']){
					foreach (explode(",", $sub_tax) as $subtax) {
					 	$st_array = explode("-",$subtax);
						$subtax_array = $this->taxation[$st_array[0]];
						$subtax_norm_name = $subtax_array['name'];
						$subtax_norm_name_rate = $subtax_norm_name."_rate";
						$subtax_norm_name_amount = $subtax_norm_name."_amount";
						
						$g->current_row[$subtax_norm_name_rate] = $subtax_array['percentage'];
						$g->current_row[$subtax_norm_name_amount] = ($subtax_array['percentage'] * $g->model['amount_excluding_tax'])/100;
					}
				}
			}

		});

		$grid->addFormatter('description','wrap');
		$grid->add('misc\Export');

	}
}