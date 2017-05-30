<?php

namespace xepan\commerce;

class Controller_GenerateRecurringInvoice extends \AbstractController {
	public $options =array();

	function run($now=null){

		if(!$now) $this->app->now;

		$recurrig_item_list = $this->add('xepan\commerce\Model_RecurringInvoiceItem')->getRows();
		// $old_qsp_detail_ids = [];

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
			// setting variable for recurring qsp detail id
			$qsp_item['recurring_from_qsp_detail_id'] = $qsp_item->id;

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
				$master_data['due_date'] = $invoice_created_at." ".date('H:i:s',strtotime($now));

				// $old_qsp_detail_ids[$master_data['id']] = [];
				unset($master_data['id']);
				$invoice_list[$qsp_item['customer_id']][$invoice_created_at]['master'] = $master_data;
			}

			// add detail data
			// if(!isset($old_qsp_detail_ids[$qsp_item['qsp_master_id']])){
			// 	$old_qsp_detail_ids[$qsp_item['qsp_master_id']] = [];
			// }
			// $old_qsp_detail_ids[$qsp_item['qsp_master_id']][$qsp_item['id']] = 0;
			$invoice_list[$qsp_item['customer_id']][$invoice_created_at]['detail'][] = $qsp_item;
		}


		foreach ($invoice_list as $cust_id => $date_wise_invoice_data) {
			foreach ($date_wise_invoice_data as $date => $invoice_data) {
				$master = $this->add('xepan\commerce\Model_QSP_Master');
				$master->createQSP($invoice_data['master'],$invoice_data['detail'],'SalesInvoice');
			}
		}

		// UPDATE table_users
		//     SET cod_user = (case when user_role = 'student' then '622057'
		//                          when user_role = 'assistant' then '2913659'
		//                          when user_role = 'admin' then '6160230'
		//                     end),
		//         date = '12082014'
		//     WHERE user_role in ('student', 'assistant', 'admin') AND
		//           cod_office = '17389551';
		
		// this code move in to create invoice
		// $query ="";
		// foreach ($old_qsp_detail_ids as $master_id => $id_pair_array) {
		// 	$query .= "UPDATE qsp_detail SET recurring_qsp_detail_id = ( CASE ";
		// 	$all_id = "";
		// 	foreach ($id_pair_array as $old_detail_id => $new_detail_id) {
		// 		$query .= "WHEN id = ".$old_detail_id." THEN '".$new_detail_id."'";
		// 		$all_id .= "'".$old_detail_id."',";
		// 	}
		// 	$query .= ' END) WHERE id IN ('.trim($all_id,',').');';
		// }

		// if($query)
		// 	$this->app->db->dsql()->expr($query)->execute();
	}
}