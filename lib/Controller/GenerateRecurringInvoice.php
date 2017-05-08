<?php

namespace xepan\commerce;

class Controller_GenerateRecurringInvoice extends \AbstractController {
	public $options =array();

	function init(){
		parent::init();

		$recurrig_item_list = $this->add('xepan\commerce\Model_RecurringInvoiceItem')->getRows();
		$old_qsp_detail_ids = [];

		$invoice_list = [];
		/*
		[
		'customer_id' => [
					'this->app->now'=>
						[
							'master'=>[],
							'detail'=>[]
			 			]
			 		]
		]
		*/
		$invoice_list = [];
		foreach ($recurrig_item_list as $key => $qsp_item) {

			$invoice_created_at = date('Y-m-d',strtotime($qsp_item['invoice_recurring_date']));

			$update_master_info = 0;
			if(!isset($invoice_list[$qsp_item['customer_id']])){
				$invoice_list[$qsp_item['customer_id']] = [$invoice_created_at=>['master'=>[],'detail'=>[]]];
				$update_master_info = 1;
				$old_invoice = $this->add('xepan\commerce\Model_SalesInvoice')->load($qsp_item['qsp_master_id']);
			}

			// add master data once at first time
			if($update_master_info){
				$master_data = $old_invoice->getRows()[0];
				$master_data['qsp_no'] = 0;
				$master_data['status'] = "Due";
				$master_data['search_string'] = "";
				$master_data['created_at'] = $invoice_created_at;
				$master_data['due_date'] = $invoice_created_at." ".date('H:i:s',strtotime($this->app->now));

				unset($master_data['id']);
				$invoice_list[$qsp_item['customer_id']][$invoice_created_at]['master'] = $master_data;
			}

			// add detail data
			array_push($old_qsp_detail_ids, $qsp_item['id']);
			unset($qsp_item['id']);
			unset($qsp_item['qsp_master_id']);
			$invoice_list[$qsp_item['customer_id']][$invoice_created_at]['detail'][] = $qsp_item;
		}

		foreach ($invoice_list as $cust_id => $date_wise_invoice_data) {
			foreach ($date_wise_invoice_data as $date => $invoice_data) {
				$master = $this->add('xepan\commerce\Model_QSP_Master');
				$new_ids = $master->createQSP($invoice_data['master'],$invoice_data['detail'],'SalesInvoice');
			}
		}

	}
}