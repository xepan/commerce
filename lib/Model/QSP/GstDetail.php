<?php

namespace xepan\commerce;

class Model_QSP_GstDetail extends \xepan\commerce\Model_QSP_Detail{

	function init(){
		parent ::init();
		$this->getElement('created_at')->caption('Invoice Date');
		$this->addExpression('qsp_serial')->set($this->refSQL('qsp_master_id')->fieldQuery('serial'));
		$this->addExpression('customer_gstin')->set(function($m,$q){
			$cst = $m->add('xepan\commerce\Model_Customer')->addCondition('id',$m->getElement('customer_id'));
			return $q->expr('[0]',[$cst->fieldQuery('gstin')]);
		});
		
		$this->addExpression('invoice_number')->set(function($m,$q){
			return $q->expr('CONCAT(IFNULL([0],"")," ",IFNULL([1],""))',[$this->getElement('qsp_serial'),$this->getElement('qsp_master')]);
		});
		$this->addExpression('state_place_of_supply')->set($this->refSQL('qsp_master_id')->fieldQuery('shipping_state'));
		$this->addExpression('product_type')->set('""');

		// $this->addExpression('cgst_rate')->set('""');
		// $this->addExpression('cgst_amount')->set('""');
		// $this->addExpression('sgst_rate')->set('""');
		// $this->addExpression('sgst_amount')->set('""');
		// $this->addExpression('igst_rate')->set('""');
		// $this->addExpression('igst_amount')->set('""');
		// $this->addExpression('cess_rate')->set('""');
		// $this->addExpression('cess_amount')->set('""');
		
		$this->addExpression('total_transaction_value')->set($this->refSQL('qsp_master_id')->fieldQuery('net_amount'));
		$this->addExpression('is_this_is_a_bill_of_supply')->set('""');
		$this->addExpression('is_this_is_a_nil_rate_exempt_non_gst_item')->set('""');
		$this->addExpression('is_reverse_charge_applicable')->set('""');
		$this->addExpression('type_of_export')->set('""');
		$this->addExpression('shipping_port_code_export')->set('""');
		$this->addExpression('shipping_bill_number_export')->set('""');
		$this->addExpression('shipping_bill_date_export')->set('""');
		$this->addExpression('has_gst_idt_tds_been_deducted')->set('""');
		$this->addExpression('my_gstin')->set('""');
		$this->addExpression('customer_billing_address')->set($this->refSQL('qsp_master_id')->fieldQuery('billing_address'));
		$this->addExpression('customer_billing_city')->set($this->refSQL('qsp_master_id')->fieldQuery('billing_city'));
		$this->addExpression('customer_billing_state')->set($this->refSQL('qsp_master_id')->fieldQuery('billing_state'));
		$this->addExpression('is_this_document_cancelled')->set(function($m,$q){
			return $q->expr('IF([0]="Canceled","Yes","No")',[$m->getElement("qsp_status")]);
		});
		$this->addExpression('is_customer_composition_dealer')->set('""');
		$this->addExpression('return_filling_period')->set('""');
		$this->addExpression('gstin_of_ecommerce_marketplace')->set('""');
		$this->addExpression('date_of_linked_advance_receipt')->set('""');
		$this->addExpression('voucher_number_of_linked_receipt')->set('""');
		$this->addExpression('adjustment_amount_of_linked_receipt')->set('""');
	}
}