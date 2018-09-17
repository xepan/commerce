<?php

namespace xepan\commerce;

class page_reports_itemsalereport extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'Item Sales Report';
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
		$m = $this->add('xepan\commerce\Model_QSP_ItemSaleReport',['from_date'=>$from_date,'to_date'=>$to_date]);
		$fields = ['name','sku','nominal','total_sales_invoice','total_sales_qty','total_sales_amount_excluding_tax','total_tax_amount','total_sales_amount'];
		$m->setOrder('total_sales_amount','desc');

		$grid->setModel($m,$fields);
		$expression_array = [];
		$grid->addTotals(['total_sales_invoice','total_sales_amount_excluding_tax','total_tax_amount','total_sales_amount']);
		// foreach ($this->add('xepan\commerce\Model_Taxation') as $mt) {
		// 	$norm_name = $this->app->normalizeName($mt['name']);
		// 	$norm_name_rate = $norm_name."_rate";
		// 	$norm_name_amount = $norm_name."_amount";

		// 	$this->taxation[$mt['id']] = [
		// 						'percentage'=>$mt['percentage'],
		// 						'name'=>$norm_name,
		// 						'sub_tax'=>$mt['sub_tax']
		// 					];
		// 	$grid->addColumn($norm_name_rate);
		// 	$grid->addColumn($norm_name_amount);
		// }


		// $grid->addHook('formatRow',function($g){
		// 	$g->current_row['description'] = nl2br(strip_tags($g->model['description']));
		// 	$tax_id = $g->model['taxation_id'];

		// 	if(isset($this->taxation[$tax_id])){
		// 		$tax_array = $this->taxation[$tax_id];
		// 		$norm_name = $tax_array['name'];
		// 		$norm_name_rate = $norm_name."_rate";
		// 		$norm_name_amount = $norm_name."_amount";

		// 		$g->current_row[$norm_name_rate] = $tax_array['percentage'];
		// 		$g->current_row[$norm_name_amount] = ($tax_array['percentage'] * $g->model['amount_excluding_tax'])/100;

		// 		if($sub_tax = $tax_array['sub_tax']){
		// 			foreach (explode(",", $sub_tax) as $subtax) {
		// 			 	$st_array = explode("-",$subtax);
		// 				$subtax_array = $this->taxation[$st_array[0]];
		// 				$subtax_norm_name = $subtax_array['name'];
		// 				$subtax_norm_name_rate = $subtax_norm_name."_rate";
		// 				$subtax_norm_name_amount = $subtax_norm_name."_amount";
						
		// 				$g->current_row[$subtax_norm_name_rate] = $subtax_array['percentage'];
		// 				$g->current_row[$subtax_norm_name_amount] = ($subtax_array['percentage'] * $g->model['amount_excluding_tax'])/100;
		// 			}
		// 		}
		// 	}

		// });

		if($form->isSubmitted()){
			$grid->js()->reload(['filter'=>1,'from_date'=>$form['from_date'],'to_date'=>$form['to_date']])->execute();
		}
				
		$options = [
			'format'=> "csv",
			'separator'=> ",",
			'filename'=> "item_sales_report_from_".$from_date."_to_".$to_date,
			'linkname'=> "Export CSV",
			'quotes'=> "\"",
			'btn_prepand_class'=>'#'.$grid->name." .xepan-filter-form"
		];

		$grid->js(true,$grid->js()->table_download($options)->_selector('#'.$grid->name." table"));

	}
}