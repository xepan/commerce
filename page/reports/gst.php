<?php

namespace xepan\commerce;

class page_reports_gst extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'GST Report';
	public $taxation = [];

	function init(){
		parent::init();
		
		$this->js()->_load('table2download');

		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$form = $this->add('Form');
		$form
			->add('xepan\base\Controller_FLC')
			->showLables(true)
				->makePanelsCoppalsible(true)
				->layout([
					'from_date'=>'c1~4',
					'to_date'=>'c2~4',
					'FormButtons'=>'c3~4'
				]);

		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');
		$form->addSubmit('Generate Report');


		$grid = $this->add('xepan\hr\Grid');
		$m = $this->add('xepan\commerce\Model_QSP_GstDetail');
		$m->addCondition('qsp_type','SalesInvoice');
		$m->addCondition([['qsp_status','Paid'],['qsp_status','Due']]);
		// $m->addCondition('is_this_document_cancelled','Yes');
		$m->getElement('created_at')->type('date');
		$fields = ['created_at','invoice_number','customer','customer_gstin','state_place_of_supply','product_type','description','hsn_sac','quantity','qty_unit','price','discount','tax_percentage','cgst_rate','cgst_amount','sgst_rate','sgst_amount','igst_rate','igst_amount','cess_rate','cess_amount','total_transaction_value','is_this_is_a_bill_of_supply','is_this_is_a_nil_rate_exempt_non_gst_item','is_reverse_charge_applicable','type_of_export','shipping_port_code_export','shipping_bill_number_export','shipping_bill_date_export','has_gst_idt_tds_been_deducted','my_gstin','customer_billing_address','customer_billing_city','customer_billing_state','is_this_document_cancelled','is_customer_composition_dealer','return_filling_period','gstin_of_ecommerce_marketplace','date_of_linked_advance_receipt','voucher_number_of_linked_receipt','adjustment_amount_of_linked_receipt','amount_excluding_tax','qsp_status'];
		
		if($_GET['filter']){
			if($from_date){
				$m->addCondition('created_at','>=',$from_date);
			}
			if($to_date){
				$m->addCondition('created_at','<',$this->app->nextDate($to_date));
			}
		}else{
			$m->addCondition('id','-1');
		}

		$grid->setModel($m,$fields);
		$expression_array = [];
		foreach ($this->add('xepan\commerce\Model_Taxation') as $mt) {
			$norm_name = $this->app->normalizeName($mt['name']);
			$norm_name_rate = $norm_name."_rate";
			$norm_name_amount = $norm_name."_amount";

			$this->taxation[$mt['id']] = [
								'percentage'=>$mt['percentage'],
								'name'=>$norm_name,
								'sub_tax'=>$mt['sub_tax']
							];
			$grid->addColumn($norm_name_rate);
			$grid->addColumn($norm_name_amount);
		}


		$grid->addHook('formatRow',function($g){
			$g->current_row['description'] = nl2br(strip_tags($g->model['description']));
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

		if($form->isSubmitted()){
			$grid->js()->reload(['filter'=>1,'from_date'=>$form['from_date'],'to_date'=>$form['to_date']])->execute();
		}
		
		$grid->addFormatter('description','wrap');
		$options = [
			'format'=> "csv",
			'separator'=> ",",
			'filename'=> "gst_report_from_".$from_date."_to_".$to_date,
			'linkname'=> "Export CSV",
			'quotes'=> "\"",
			'btn_prepand_class'=>'#'.$grid->name." .xepan-filter-form"
		];

		$grid->js(true,$grid->js()->table_download($options)->_selector('#'.$grid->name." table"));

		// $options2 = [
		// 			'format' => "xls",
		// 			'btn_prepand_class' => '#'.$grid->name." .xepan-filter-form",
		// 			'linkname'=> "Export XLS",
		// 		];

		// $grid->js(true,$grid->js()->table_download($options2)->_selector('#'.$grid->name." table"));
		// $grid->add('misc\Export',['export_fields'=>$fields]);

	}
}