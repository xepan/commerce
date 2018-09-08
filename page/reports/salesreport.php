<?php

namespace xepan\commerce;

class page_reports_salesreport extends \xepan\base\Page{

	public $title = "Sales Report";
	public $sub_type_1_fields=[];
	public $sub_type_1_norm_unnorm_array=[];
	public $sub_type_2_fields=[];
	public $sub_type_2_norm_unnorm_array=[];
	public $sub_type_3_fields=[];
	public $sub_type_3_norm_unnorm_array=[];
	public $communication_fields=[];
	public $communication_type_value = ['Email'=>'Email','Comment'=>'Comment','Call'=>'Call','Personal'=>'Meeting','Sms'=>'SMS','TeleMarketing'=>'TeleMarketing'];

	public $config_m;
	public $model;
	public $model_field_array=[];
	public $show_filter_form=true;
	public $from_date;
	public $to_date;
	public $emp_id;
	public $department_id;
	public $f_lead_count;
	public $f_document_date;
	public $f_sale_order;
	public $f_sale_invoice;
	public $f_amount;

	public $return_sale_order_group_concat=false;
	public $return_sale_invoice_group_concat=false;
	public $sale_order_cancel_fields = [];
	public $sale_invoice_cancel_fields = [];

	function init(){
		parent::init();

		// sticky get param
		$this->emp_id = $this->app->stickyGET('employee_id');
		$this->from_date = $from_date  = $this->app->stickyGET('from_date')?:$this->app->today;
		$this->to_date = $to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$this->department_id = $department = $this->app->stickyGET('department');
		$this->f_lead_count = $this->app->stickyGET('f_lead_count')?:"both";
		$this->f_document_date = $this->app->stickyGET('f_document_date')?:"only_date_range";
		$this->f_sale_order = $this->app->stickyGET('f_sale_order')?:"all";
		$this->f_sale_invoice = $this->app->stickyGET('f_sale_invoice')?:"all";
		$this->f_amount = $this->app->stickyGET('f_amount')?:"based_on_saleinvoice";
		$this->selected_employee_id = $this->app->stickyGET('selected_employee_id');
		$this->show_unique_lead = $this->app->stickyGET('show_unique_lead');
		$this->order_status = $this->app->stickyGET('order_status');
		$this->invoice_status = $this->app->stickyGET('invoice_status');
		$this->f_display_mode = $this->app->stickyGET('f_display_mode')?:'all';

		
		$qsp_can_model = $this->add('xepan\commerce\Model_Config_QSPCancelReason');
		$qsp_can_model->addCondition('for','SalesOrder');
		$qsp_can_model->tryLoadAny();
		$this->order_cancel_reason = explode(",", $qsp_can_model['name']);

		$qsp_can_model = $this->add('xepan\commerce\Model_Config_QSPCancelReason');
		$qsp_can_model->addCondition('for','SalesInvoice');
		$qsp_can_model->tryLoadAny();
		$this->invoice_cancel_reason = explode(",", $qsp_can_model['name']);
		
		$this->config_m = $this->add('xepan\communication\Model_Config_SubType');
		$this->config_m->tryLoadAny();

		// subtype 1
		foreach(explode(",", $this->config_m['sub_type']) as $subtypes) {
			$subtype_name = $this->app->normalizeName(trim($subtypes));
			$this->sub_type_1_norm_unnorm_array[$subtype_name] = $subtypes;
		}

		foreach(explode(",", $this->config_m['calling_status']) as $subtypes) {
			$subtype_name = $this->app->normalizeName(trim($subtypes));
			$this->sub_type_2_norm_unnorm_array[$subtype_name] = $subtypes;
		}

		foreach(explode(",", $this->config_m['sub_type_3']) as $subtypes) {
			$subtype_name = $this->app->normalizeName(trim($subtypes));
			$this->sub_type_3_norm_unnorm_array[$subtype_name] = $subtypes;
		}
				
	}

	function page_index(){
		// parent::init();

		if($this->show_filter_form){			
			$form = $this->add('Form');
			$form->add('xepan\base\Controller_FLC')
				->makePanelsCoppalsible(true)
				->layout([
					'date_range'=>'Filter~c1~3',
					'employee'=>'c2~3',
					'department'=>'c3~3',
					'display_mode'=>'c41~3',

					'lead_count'=>'c4~2',
					'document_date'=>'c5~2',
					'sale_order'=>'c6~2',
					'sale_invoice'=>'c7~2',
					'amount'=>'c8~2',
					'FormButtons~&nbsp;'=>'f4~2'
				]);

			$date = $form->addField('DateRangePicker','date_range');
			$set_date = $this->app->today." to ".$this->app->today;
			if($this->from_date){
				$set_date = $this->from_date." to ".$this->to_date;
				$date->set($set_date);
			}
				
			$display_mode_field = $form->addField('DropDown','display_mode')->setValueList(['all'=>'All','communication'=>'Communication only','sales'=>'Sales Only']);

			$post_model = $this->app->employee->ref('post_id');
			$employee_model = $this->add('xepan\hr\Model_Employee',['title_field'=>'name_with_post'])
								->addCondition('status','Active');
			$employee_model->addExpression('name_with_post')->set(function($m,$q){
				return $q->expr('CONCAT_WS("::",[name],[post],[code])',
							[
								'name'=>$m->getElement('name'),
								'post'=>$m->getElement('post'),
								'code'=>$m->getElement('code')
							]
						);
			});	

			$emp_field = $form->addField('xepan\base\Basic','employee');

			if($this->emp_id){
				$emp_field->set($this->emp_id);
				$emp_field->other_field->setAttr('disabled','disabled');
			}
					
			$dept_field = $form->addField('xepan\base\DropDown','department');
			$model_department = $this->add('xepan\hr\Model_Department');

			switch ($post_model['permission_level']) {
				case "Department":
					$model_department->addCondition('id',$this->app->employee['department_id']);
					$dept_field->set($this->app->employee['department_id']);
					$dept_field->setAttr('disabled',true);
					$this->department_id = $department = $this->app->employee['department_id'];

					$employee_model->addCondition('department_id',$this->app->employee['department_id']);
					break;
				case ($post_model['permission_level'] == 'Individual' || $post_model['permission_level'] == 'Sibling'):
					$model_department->addCondition('id',$this->app->employee['department_id']);
					$dept_field->set($this->app->employee['department_id']);
					$dept_field->setAttr('disabled',true);
					$this->department_id = $department = $this->app->employee['department_id'];

					$employee_model->addCondition('id',$this->app->employee->id);
					$emp_field->set($this->app->employee->id);
					$emp_field->other_field->setAttr('disabled',true);
					$this->emp_id = $emp_id = $this->app->employee->id;
					break;
			}

			$dept_field->setModel($model_department);
			$dept_field->setEmptyText('All');

			$emp_field->setModel($employee_model);

			$lead_count_field = $form->addField('DropDown','lead_count')->setValueList(['created_by'=>'Created By Employee','assign_to_emp'=>'Assign to Employee','both'=>'Both'])->set($this->f_lead_count);
			$document_date_field = $form->addField('DropDown','document_date')->setValueList(['only_date_range'=>'Created between date range only','all'=>'All'])->set($this->f_document_date);
			$sale_order_field = $form->addField('DropDown','sale_order')->setValueList(['based_on_lead'=>'Sale Orders of related leads','created_by'=>'Created By Self','all'=>'All'])->set($this->f_sale_order);
			$sale_invoice_field = $form->addField('DropDown','sale_invoice')->setValueList(['based_on_lead'=>'Sale Invoices of related leads','created_by'=>'Created By Self','related_to_saleorder'=>'Related to Sale Order','all'=>'All'])->set($this->f_sale_invoice);
			$amount = $form->addField('DropDown','amount')->setValueList(['based_on_saleinvoice'=>'Based on Sale Invoice','total_lead_balance'=>'Based on lead account balance (on account)'])->set($this->f_amount);

			$form->addSubmit('Get Details')->addClass('btn btn-primary');
		}
		
		$this->setModel();
		// grid

		// throw new \Exception($this->f_display_mode);
		
		$grid = $this->add('xepan\hr\Grid');

		$grid->setModel($this->model,$this->model_field_array);
		$order = $grid->addOrder();
		$grid->addpaginator(10);
		$grid->add('misc/Export',['export_fields'=>$this->model_field_array]);
		// $grid->template->tryDel('Pannel');
		

		if($this->show_filter_form){
			if($form->isSubmitted()){
				$grid->js()->reload(
						[
							'employee_id'=>$form['employee'],
							'from_date'=>$date->getStartDate()?:0,
							'to_date'=>$date->getEndDate()?:0,
							'department'=>$form['department'],
							'f_lead_count'=>$form['lead_count'],
							'f_document_date'=>$form['document_date'],
							'f_sale_order'=>$form['sale_order'],
							'f_sale_invoice'=>$form['sale_invoice'],
							'f_amount'=>$form['amount'],
							'f_display_mode'=>$form['display_mode']
						]
				)->execute();
			}
		}

		// grid formatter
		$grid->addHook('formatRow',function($g){

			// unique lead count
			$tmp = array_merge(explode(",", $g->model['unique_leads_from']), explode(",", $g->model['unique_leads_to']));
			$tmp = array_unique($tmp);
			$unique_lead_count = count($tmp);
			if($unique_lead_count == 1 && !$tmp[0]){
				$unique_lead_count = 0;
			}
			$g->current_row_html['unique_lead'] = '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Communictaed with unique Leads('.$unique_lead_count.') by employee: '.$g->model['name'],$g->api->url('./commlead',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'show_unique_lead'=>1))).'">'.$unique_lead_count.'</a><br/>';
			// total lead count
			$g->current_row_html['total_lead_count'] = '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Leads',$g->api->url('./commlead',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'lead_count'=>$this->f_lead_count))).'">'.$g->model['total_lead_count'].'</a>';
			
			// $communication_graph_data = $g->model['total_email'].",".$g->model['total_call'].",".$g->model['total_telemarketing'].",".$g->model['total_sms'].",".$g->model['total_meeting'].",".$g->model['total_comment'];
			$communication_graph_data = [];
			$communication_graph_data_label = [];
			$comm_label_str = "";
			foreach ($this->communication_type_value as $key => $value) {
				$total_field_name = "total_".strtolower($value);

				if($g->model[$total_field_name]){
					$comm_label_str .= '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL($value.' communication history of employee '.$g->model['name'],$g->api->url('./commdegging',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'communication_type'=>$key))).'">'.$value.": ".$g->model[$total_field_name].'</a><br/>';
					$communication_graph_data[] = $g->model[$total_field_name];
					$communication_graph_data_label[] = $value.": ".$g->model[$total_field_name];
				}			
			}
			if($g->model['attended_others_meeting']){
				$comm_label_str .= '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Join Meeting communication history of employee '.$g->model['name'],$g->api->url('./commdegging',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'communication_type'=>'Meeting Join'))).'">'."Meeting Join: ".$g->model['attended_others_meeting'].'</a><br/>';
			}

			// sale order 
			$g->current_row_html['sale_order_detail'] = '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Sale Order of employee '.$g->model['name'],$g->api->url('./commsaleorder',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id']))).'">'.implode("<br/>",explode(",", $g->model['sale_order_detail'])).'</a><br/>';
			$g->current_row_html['total_sale_invoice'] = '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Sale Invoice of employee '.$g->model['name'],$g->api->url('./commsaleinvoice',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id']))).'">'.'Invoice : '.$g->model['total_sale_invoice']."<br/>Amount: ".$g->model['total_sale_invoice_amount'].'</a><br/>';

			// cancel detail
			$soc_str = "";
			$soc_total = 0;
			$soc_total_amount = 0;
			foreach ($this->sale_order_cancel_fields as $name => $normalize_name) {
				if(!$g->model[$normalize_name]) continue;
				$soc_str .= $name.": ".$g->model[$normalize_name]." (".$g->model[$normalize_name."_amount"].")<br/>";
				$soc_total += $g->model[$normalize_name];
				$soc_total_amount += $g->model[$normalize_name."_amount"];
			}
			$g->current_row_html['sale_order_canceled_details'] = '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Canceled Sale Orders of employee '.$g->model['name'],$g->api->url('./commsaleorder',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'order_status'=>'Canceled'))).'">'."Cancel Order: ".$soc_total." (".$soc_total_amount.")".'</a><br/>'.$soc_str;

			$sic_str = "";
			$sic_total = 0;
			$sic_total_amount = 0;
			foreach ($this->sale_invoice_cancel_fields as $name => $normalize_name) {
				if(!$g->model[$normalize_name]) continue;
				$sic_str .= $name.": ".$g->model[$normalize_name]." (".$g->model[$normalize_name."_amount"].")<br/>";
				$sic_total += $g->model[$normalize_name];
				$sic_total_amount += $g->model[$normalize_name."_amount"];
			}
			$g->current_row_html['sale_invoice_canceled_details'] = '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL('Canceled Sale Invoices of employee '.$g->model['name'],$g->api->url('./commsaleinvoice',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'invoice_status'=>'Canceled'))).'">'."Cancel Invoice: ".$sic_total." (".$sic_total_amount.")".'</a><br/>'.$sic_str;

			// sub type 1
			$sub_type_1_label_str = "";
			$sub_type_1_graph_data = [];
			$sub_type_1_graph_data_label = [];
			foreach ($this->sub_type_1_fields as $name) {
				if(!$g->model[$name]) continue;

				$sub_type_1_graph_data[] = $g->model[$name];
				$sub_type_1_graph_data_label[] = $name.": ".$g->model[$name];
				$sub_type_1_label_str .= '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL($name.' communication history of employee '.$g->model['name'],$g->api->url('./commdegging',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'sub_type_1'=>$name))).'"> '.$name.": ".$g->model[$name].'</a><br/>';
			}

			// sub type 2
			$sub_type_2_label_str = "";
			$sub_type_2_graph_data = [];
			$sub_type_2_graph_data_label = [];
			foreach ($this->sub_type_2_fields as $name) {
				if(!$g->model[$name]) continue;

				$sub_type_2_graph_data[] = $g->model[$name];
				$sub_type_2_graph_data_label[] = $name.": ".$g->model[$name];
				$sub_type_2_label_str .= '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL($name.' communication history of employee '.$g->model['name'],$g->api->url('./commdegging',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'sub_type_2'=>$name))).'"> '.$name.": ".$g->model[$name].'</a><br/>';
			}
			
			// sub type 3
			$sub_type_3_graph_data = [];
			$sub_type_3_graph_data_label = [];
			$sub_type_3_label_str = "";
			foreach ($this->sub_type_3_fields as $name) {
				if(!$g->model[$name]) continue;

				$sub_type_3_graph_data[] = $g->model[$name];
				$sub_type_3_graph_data_label[] = $name.": ".$g->model[$name];
				$sub_type_3_label_str .= '<a href="javascript:void(0);" onclick="'.$g->js()->univ()->frameURL($name.' communication history of employee '.$g->model['name'],$g->api->url('./commdegging',array('from_date'=>$this->from_date,'to_date'=>$this->to_date,'selected_employee_id'=>$g->model['id'],'sub_type_3'=>$name))).'"> '.$name.": ".$g->model[$name].'</a><br/>';
			}
			

			$g->current_row_html['communication'] = '<div class="row"><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12"><div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline communication"></div></div><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12"> <small>'.$comm_label_str."</small></div></div>";
			$g->current_row_html['subtype_1'] = '<div class="row"><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12" > <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline subtype1"></div></div><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12"><small>'.$sub_type_1_label_str."</small></div></div>";
			$g->current_row_html['subtype_2'] = '<div class="row"><div  class="col-md-12 col-xs-12 col-lg-12 col-sm-12" > <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline subtype2"></div></div><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12"><small>'.$sub_type_2_label_str."</small></div></div>";
			$g->current_row_html['subtype_3'] = '<div class="row"><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12" > <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline subtype3"></div></div><div class="col-md-12 col-xs-12 col-lg-12 col-sm-12"><small>'.$sub_type_3_label_str."</small></div></div>";
			$g->setTDParam('communication','style','vertical-align:top;');
			$g->setTDParam('subtype_1','style','vertical-align:top;white-space:nowrap;');
			$g->setTDParam('subtype_2','style','vertical-align:top;white-space:nowrap;');
			$g->setTDParam('subtype_3','style','vertical-align:top;white-space:nowrap;');

			if(count($communication_graph_data_label)){
				$g->js(true)->_selector('.sparkline.communication[data-id='.$g->model->id.']')
					->sparkline($communication_graph_data, [
						'enableTagOptions' => true,
						'tooltipFormat'=>'{{offset:offset}} ({{percent.1}}%)',
						'tooltipValueLookups'=>['offset'=>$communication_graph_data_label]
					]);
			}

			if(count($sub_type_1_graph_data_label)){
				$g->js(true)->_selector('.sparkline.subtype1[data-id='.$g->model->id.']')
					->sparkline($sub_type_1_graph_data, [
						'enableTagOptions' => true,
						'tooltipFormat'=>'{{offset:offset}} ({{percent.1}}%)',
						'tooltipValueLookups'=>['offset'=>$sub_type_1_graph_data_label]
					]);
			}

			if(count($sub_type_2_graph_data_label)){
				$g->js(true)->_selector('.sparkline.subtype2[data-id='.$g->model->id.']')
					->sparkline($sub_type_2_graph_data, [
						'enableTagOptions' => true,
						'tooltipFormat'=>'{{offset:offset}} ({{percent.1}}%)',
						'tooltipValueLookups'=>['offset'=>$sub_type_2_graph_data_label]
					]);
			}

			if(count($sub_type_3_graph_data_label)){
				$g->js(true)->_selector('.sparkline.subtype3[data-id='.$g->model->id.']')
					->sparkline($sub_type_3_graph_data, [
						'enableTagOptions' => true,
						'tooltipFormat'=>'{{offset:offset}} ({{percent.1}}%)',
						'tooltipValueLookups'=>['offset'=>$sub_type_3_graph_data_label]
					]);
			}

		});
	
		$grid->removeColumn('unique_leads_to');
		$grid->removeColumn('unique_leads_from');
		$grid->removeColumn('total_sale_invoice_amount');

		foreach ($this->communication_fields as $name) {
			$grid->removeColumn($name);
		}
		foreach ($this->sub_type_1_fields as $name) {
			$grid->removeColumn($name);
		}
		foreach ($this->sub_type_2_fields as $name) {
			$grid->removeColumn($name);
		}
		foreach ($this->sub_type_3_fields as $name) {
			$grid->removeColumn($name);
		}
		
		foreach ($this->sale_order_cancel_fields as $name) {
			$grid->removeColumn($name);
			$grid->removeColumn($name."_amount");
		}
		foreach ($this->sale_invoice_cancel_fields as $name) {
			$grid->removeColumn($name);
			$grid->removeColumn($name."_amount");
		}

		$grid->js(true)->_load('jquery.sparkline.min');

	}

	function page_commdegging(){
		$employee_id = $this->app->stickyGET('selected_employee_id');
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');

		$communication_type = $this->app->stickyGET('communication_type');
		$sub_type_1 = $this->app->stickyGET('sub_type_1');
		$sub_type_2 = $this->app->stickyGET('sub_type_2');
		$sub_type_3 = $this->app->stickyGET('sub_type_3');

		$comm_model = $this->add('xepan\communication\Model_Communication');
		$comm_model->addCondition('created_by_id',$employee_id);

		
		if($communication_type == "Meeting Join"){
			$rel_emp_model = $this->add('xepan\communication\Model_CommunicationRelatedEmployee',['table_alias'=>'employee_commni_other_meeting']);
			$rel_emp_model->addCondition('employee_id',$employee_id)
					->addCondition('comm_created_at','>=',$this->from_date)
					->addCondition('comm_created_at','<',$this->api->nextDate($this->to_date))
					;
			$comm_ids = $rel_emp_model->_dsql()->del('fields')->field('communication_id')->getAll();
			$comm_ids = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($comm_ids)),false);

			$comm_model->addCondition('id','in',$comm_ids);
						
		}elseif($communication_type){
			$comm_model->addCondition('communication_type',$communication_type);
		}

		if($sub_type_1)
			$comm_model->addCondition('sub_type',trim($this->sub_type_1_norm_unnorm_array[$sub_type_1]));
		if($sub_type_2)
			$comm_model->addCondition('calling_status',trim($this->sub_type_2_norm_unnorm_array[$sub_type_2]));
		if($sub_type_3)
			$comm_model->addCondition('sub_type_3',trim($this->sub_type_3_norm_unnorm_array[$sub_type_3]));

		if($from_date)
			$comm_model->addCondition('created_at','>=',$from_date);
		if($to_date)
			$comm_model->addCondition('created_at','<',$this->app->nextDate($to_date));

		$comm_model->setOrder('id','desc');

		$form = $this->add('Form');
		$layout_array = [];
		if(!$communication_type)
			$layout_array['communication_type'] = 'Filter~c1~3';
		if(!$sub_type_1)
			$layout_array['sub_type_1~'.($this->config_m['sub_type_1_label_name']?:"Sub Type 1")] = 'c2~3';
		if(!$sub_type_2)
			$layout_array['sub_type_2~'.($this->config_m['sub_type_2_label_name']?:"Sub Type 2")] = 'c3~3';
		if(!$sub_type_3)
			$layout_array['sub_type_3~'.($this->config_m['sub_type_3_label_name']?:"Sub Type 3")] = 'c4~3';

		$layout_array['FormButtons~&nbsp;'] = 'c5~3';

		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->addContentSpot()
			->makePanelsCoppalsible(true)
			->layout($layout_array);
				

		if(!$communication_type){
			$form->addField('DropDown','communication_type')
				->setValueList($this->communication_type_value)
				->setEmptyText('Please Select');
		}
		// $this->config_m['sub_type_1_label_name']
		if(!$sub_type_1){
			$form->addField('DropDown','sub_type_1')->setValueList($this->sub_type_1_norm_unnorm_array)->setEmptyText('Please Select ...');
		}
		if(!$sub_type_2){
			$form->addField('DropDown','sub_type_2')->setValueList($this->sub_type_2_norm_unnorm_array)->setEmptyText('Please Select ...');
		}
		if(!$sub_type_3){
			$form->addField('DropDown','sub_type_3')->setValueList($this->sub_type_3_norm_unnorm_array)->setEmptyText('Please Select ...');
		}

		$form->addSubmit('Filter')->addClass('btn btn-primary');

		$grid = $this->add('xepan\base\Grid');
		$grid->template->tryDel('Pannel');
		$grid->setModel($comm_model,['title','description','created_at','from','to','created_by','sub_type','calling_status','sub_type_3']);
		$grid->addPaginator(25);


		if($form->isSubmitted()){
			$reload_param = [];

			if(!$communication_type)
				$reload_param['communication_type'] = $form['communication_type'];
			if(!$sub_type_1)
				$reload_param['sub_type_1'] = $form['sub_type_1'];
			if(!$sub_type_2)
				$reload_param['sub_type_2'] = $form['sub_type_2'];
			if(!$sub_type_3)
				$reload_param['sub_type_3'] = $form['sub_type_3'];

			$grid->js()->reload($reload_param)->execute();
		}

	}

	function setModel(){

		// record model
		$emp_model = $this->add('xepan\communication\Model_EmployeeCommunication',['from_date'=>$this->from_date,'to_date'=>$this->to_date]);
		$emp_model->addCondition('status','Active');
		if($this->emp_id){
			$emp_model->addCondition('id',$this->emp_id);
		}elseif($this->selected_employee_id)
			$emp_model->addCondition('id',$this->selected_employee_id);

		if($this->from_date){
			$emp_model->from_date = $this->from_date;
		}
		if($this->to_date){
			$emp_model->to_date = $this->to_date;
		}
		if($this->department_id){
			$emp_model->addCondition('department_id',$this->department_id);
		}


		$this->communication_fields = ['total_email','total_comment','total_meeting','total_sms','total_telemarketing','attended_others_meeting','total_call','dial_call','received_call'];
		/*Communication Sub Type Form */
		$this->model_field_array = ['name','total_lead_count','unique_lead'];			

		// filter values are : 'created_by'=>'Created By Employee','assign_to_emp'=>'Assign to Employee','both'=>'Both'
		$emp_model->addExpression('total_lead_ids')->set(function($m,$q){
			$contact = $m->add('xepan\base\Model_Contact',['table_alias'=>'commallcontact']);
			$contact->addCondition([['type','Contact'],['type','Lead'],['type','Customer']]);

			if($this->f_lead_count == "created_by"){
				$contact->addCondition('created_at','>=',$this->from_date)
					->addCondition('created_at','<',$this->api->nextDate($this->to_date))
					->addCondition('created_by_id',$m->getElement('id'));
			}elseif($this->f_lead_count == "assign_to_emp"){
				$contact->addCondition('assign_at','>=',$this->from_date)
					->addCondition('assign_at','<',$this->api->nextDate($this->to_date))
					->addCondition('assign_to_id',$m->getElement('id'));
			}else{
				$contact->addCondition([['created_by_id',$m->getElement('id')],['assign_to_id',$m->getElement('id')]]);
				$contact->addCondition([
						['created_at','>=',$this->from_date],
						['assign_at','>=',$this->from_date]
					]);
				$contact->addCondition([
						['created_at','<',$this->app->nextDate($this->to_date)],
						['assign_at','<',$this->app->nextDate($this->to_date)]
					]);
			}
			return $contact->_dsql()->del('fields')->field($q->expr('[0]',[$contact->getElement('id')]));
		});

		$emp_model->addExpression('total_lead_count')->set(function($m,$q){
			$contact = $m->add('xepan\base\Model_Contact',['table_alias'=>'lead_count']);
			$contact->addCondition([['type','Contact'],['type','Lead'],['type','Customer']]);

			if($this->f_lead_count == "created_by"){
				$contact->addCondition('created_at','>=',$this->from_date)
					->addCondition('created_at','<',$this->api->nextDate($this->to_date))
					->addCondition('created_by_id',$m->getElement('id'));
			}elseif($this->f_lead_count == "assign_to_emp"){
				$contact->addCondition('assign_at','>=',$this->from_date)
					->addCondition('assign_at','<',$this->api->nextDate($this->to_date))
					->addCondition('assign_to_id',$m->getElement('id'));
			}else{
				$contact->addCondition([['created_by_id',$m->getElement('id')],['assign_to_id',$m->getElement('id')]]);
				$contact->addCondition([
						['created_at','>=',$this->from_date],
						['assign_at','>=',$this->from_date]
					]);
				$contact->addCondition([
						['created_at','<',$this->app->nextDate($this->to_date)],
						['assign_at','<',$this->app->nextDate($this->to_date)]
					]);
			}
			return $contact->count();
		});

		$emp_model->addExpression('unique_lead')->set('""')->caption('comm. with unique lead');

		if($this->f_display_mode == "all" or $this->f_display_mode == "communication"){
			$this->model_field_array = array_merge($this->model_field_array,['communication','total_email','total_comment','total_meeting','total_sms','total_telemarketing','attended_others_meeting','total_call','dial_call','received_call','unique_leads_from','unique_leads_to']);

			$emp_model->addExpression('communication')->set('""');
			// sub type 1
			$emp_model->addExpression('subtype_1')->set('""')->caption($this->config_m['sub_type_1_label_name']?:"Sub Type 1");
			$this->model_field_array[] = "subtype_1";
			foreach (explode(",", $this->config_m['sub_type']) as $subtypes) {
				// $grid->addColumn($this->app->normalizeName($subtypes));
				$subtypes = trim($subtypes);
				$subtype_name = $this->app->normalizeName($subtypes);
				$this->sub_type_1_fields[] = $subtype_name;
				$this->model_field_array[] = $subtype_name;

				$emp_model->addExpression($subtype_name)->set(function($m,$q)use($subtypes){
					return $m->add('xepan\communication\Model_Communication')
								->addCondition('created_by_id',$q->getfield('id'))
								->addCondition('sub_type',$subtypes)
								->addCondition('created_at','>=',$this->from_date)
								->addCondition('created_at','<',$this->api->nextDate($this->to_date))
								->count();
				});
			}
			// sub type 2
			$emp_model->addExpression('subtype_2')->set('""')->caption($this->config_m['sub_type_2_label_name']?:"Sub Type 2");
			$this->model_field_array[] = "subtype_2";
			foreach (explode(",", $this->config_m['calling_status']) as $callingstatus) {
				// $grid->addColumn($this->app->normalizeName($callingstatus));
				$callingstatus = trim($callingstatus);
				$subtype_name = $this->app->normalizeName($callingstatus);
				$this->model_field_array[] = $subtype_name;	
				$this->sub_type_2_fields[] = $subtype_name;

				$emp_model->addExpression($subtype_name)->set(function($m,$q)use($callingstatus){
						return $m->add('xepan\communication\Model_Communication')
									->addCondition('created_by_id',$q->getfield('id'))
									->addCondition('calling_status',$callingstatus)
									->addCondition('created_at','>=',$this->from_date)
									->addCondition('created_at','<',$this->api->nextDate($this->to_date))
									->count();
					});

			}
			// sub type 3
			$emp_model->addExpression('subtype_3')->set('""')->caption($this->config_m['sub_type_3_label_name']?:"Sub Type 3");
			$this->model_field_array[] = "subtype_3";
			foreach (explode(",", $this->config_m['sub_type_3']) as $sub_type_3) {
				// $grid->addColumn($this->app->normalizeName($callingstatus));
				$sub_type_3 = trim($sub_type_3);
				$subtype_name = $this->app->normalizeName($sub_type_3);
				$this->model_field_array[] = $subtype_name;
				$this->sub_type_3_fields[] = $subtype_name;
				$emp_model->addExpression($subtype_name)->set(function($m,$q)use($sub_type_3){
						return $m->add('xepan\communication\Model_Communication')
									->addCondition('created_by_id',$q->getfield('id'))
									->addCondition('sub_type_3',$sub_type_3)
									->addCondition('created_at','>=',$this->from_date)
									->addCondition('created_at','<',$this->api->nextDate($this->to_date))
									->count();
					});
			}
		}
		
		if($this->f_display_mode =="all" OR $this->f_display_mode == "sales"){
			$this->model_field_array = array_merge($this->model_field_array,['sale_order_detail','sale_order_canceled_details','total_sale_invoice','total_sale_invoice_amount','sale_invoice_canceled_details','amount_balance']);
			// document date: 'only_date_range'=>'Apply Date','all'=>'All'
			// sale_order: 'based_on_lead'=>'Based on lead count','created_by'=>'Created By Self','all'=>'All'
			$emp_model->addExpression('sale_order_ids')->set(function($m,$q){
				$so = $m->add('xepan\commerce\Model_SalesOrder',['table_alias'=>'sosrids']);
				$so->addCondition('status','in',['Approved','InProgress','UnderDispatch','Completed','OnlineUnpaid']);

				if($this->f_document_date == "only_date_range"){
					$so->addCondition('created_at','>=',$this->from_date);
					$so->addCondition('created_at','<',$this->app->nextDate($this->to_date));
				}

				if($this->f_sale_order == "created_by"){
					$so->addCondition('created_by_id',$m->getElement('id'));
				}elseif($this->f_sale_order == 'based_on_lead'){
					$so->addCondition('contact_id','in',$m->getElement('total_lead_ids'));
				}else{
					$so->addCondition([
									['created_by_id',$m->getElement('id')],
									['contact_id','in',$m->getElement('total_lead_ids')]
								]);
				}

				if($this->return_sale_order_group_concat)
					return $so->_dsql()->del('fields')->field($q->expr('group_concat([0])',[$so->getElement('id')]));

				return $so->_dsql()->del('fields')->field($q->expr('[0]',[$so->getElement('id')]));
			});

			$emp_model->addExpression('sale_order_detail')->set(function($m,$q){
				$so = $m->add('xepan\commerce\Model_SalesOrder',['table_alias'=>'sosr']);
				$so->addCondition('status','in',['Approved','InProgress','UnderDispatch','Completed','OnlineUnpaid']);

				if($this->f_document_date == "only_date_range"){
					$so->addCondition('created_at','>=',$this->from_date);
					$so->addCondition('created_at','<',$this->app->nextDate($this->to_date));
				}

				if($this->f_sale_order == "created_by"){
					$so->addCondition('created_by_id',$m->getElement('id'));
				}elseif($this->f_sale_order == 'based_on_lead'){
					$so->addCondition('contact_id','in',$m->getElement('total_lead_ids'));
				}else{
					$so->addCondition([
									['created_by_id',$m->getElement('id')],
									['contact_id','in',$m->getElement('total_lead_ids')]
								]);
				}
				return $q->expr('CONCAT("Order: ",IFNULL([0],0),",Amount: ",IFNULL([1],0))',[$so->count(),$so->sum('net_amount')]);
			});

			$emp_model->addExpression('sale_order_canceled_ids')->set(function($m,$q){
				$so = $m->add('xepan\commerce\Model_SalesOrder',['table_alias'=>'sosrcan']);
				$so->addCondition('status','Canceled');

				if($this->f_document_date == "only_date_range"){
					$so->addCondition('created_at','>=',$this->from_date);
					$so->addCondition('created_at','<',$this->app->nextDate($this->to_date));
				}

				if($this->f_sale_order == "created_by"){
					$so->addCondition('created_by_id',$m->getElement('id'));
				}elseif($this->f_sale_order == 'based_on_lead'){
					$so->addCondition('contact_id','in',$m->getElement('total_lead_ids'));
				}else{
					$so->addCondition([
									['created_by_id',$m->getElement('id')],
									['contact_id','in',$m->getElement('total_lead_ids')]
								]);
				}

				if($this->return_sale_order_group_concat)
					return $so->_dsql()->del('fields')->field($q->expr('group_concat([0])',[$so->getElement('id')]));

				return $so->_dsql()->del('fields')->field($q->expr('[0]',[$so->getElement('id')]));
				// return $q->expr('CONCAT("Order: ",IFNULL([0],0),",Amount: ",IFNULL([1],0))',[$so->count(),$so->sum('net_amount')]);
			});

			$emp_model->addExpression('sale_order_canceled_details')->set('""');

			// sale order cancel reason
			foreach ($this->order_cancel_reason as $name) {
				if(!trim($name)) continue;
				$normalize_name = 'soc_'.$this->app->normalizeName($name);

				$this->sale_order_cancel_fields[$name] = $normalize_name;
				$this->model_field_array[] = $normalize_name;

				$emp_model->addExpression($normalize_name)->set(function($m,$q)use($name,$normalize_name){
					$soc = $this->add('xepan\commerce\Model_SalesOrder',['table_alias'=>$normalize_name])
					    ->addCondition('status','Canceled')
					    ->addCondition('cancel_reason',$name)
					    ->addCondition('id','in',$m->getElement('sale_order_canceled_ids'))
					    ;
					return $soc->count();
					// return $q->expr('contact(IFNULL([0],0),",",IFNULL([1],0))',[$soc,$soc->sum('net_amount')]);
				})->caption($name);


				$normalize_name = $normalize_name."_amount";
				$this->model_field_array[] = $normalize_name;
				// $this->sale_order_cancel_fields[$name."_amount"] = $normalize_name;
				$emp_model->addExpression($normalize_name)->set(function($m,$q)use($name,$normalize_name){
					$soc = $this->add('xepan\commerce\Model_SalesOrder',['table_alias'=>$normalize_name])
					    ->addCondition('status','Canceled')
					    ->addCondition('cancel_reason',$name)
					    ->addCondition('id','in',$m->getElement('sale_order_canceled_ids'))
					    ;
					return $q->expr('IFNULL([0],0)',[$soc->sum('net_amount')]);
				})->caption($name." Amount");
			}
			

			// document date: 'only_date_range'=>'Apply Date','all'=>'All'
			// sale_invoice: 'based_on_lead'=>'Based on lead count','created_by'=>'Created By Self','related_to_saleorder'=>'Related to Sale Order','all'=>'All'
			$emp_model->addExpression('sale_invoice_ids')->set(function($m,$q){
				$so = $m->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'sisrids']);
				$so->addCondition('status','in',['Due','Paid']);

				if($this->f_document_date == "only_date_range"){
					$so->addCondition('created_at','>=',$this->from_date);
					$so->addCondition('created_at','<',$this->app->nextDate($this->to_date));
				}
				
				if($this->f_sale_invoice == "created_by"){
					$so->addCondition('created_by_id',$m->getElement('id'));
				}elseif($this->f_sale_invoice == 'based_on_lead'){
					$so->addCondition('contact_id','in',$m->getElement('total_lead_ids'));
				}elseif($this->f_sale_invoice == 'related_to_saleorder'){
					$so->addCondition('related_qsp_master_id','in',$m->getElement('sale_order_ids'));
				}else{
					$so->addCondition([
									['created_by_id',$m->getElement('id')],
									['contact_id','in',$m->getElement('total_lead_ids')],
									['related_qsp_master_id','in',$m->getElement('sale_order_ids')]
								]);
				}
				
				if($this->return_sale_invoice_group_concat)
					return $so->_dsql()->del('fields')->field($q->expr('group_concat([0])',[$so->getElement('id')]));
					
				return $so->_dsql()->del('fields')->field($q->expr('[0]',[$so->getElement('id')]));
			});

			$emp_model->addExpression('total_sale_invoice')->set(function($m,$q){
				$si_model = $m->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'saintotal']);
				$si_model->addCondition('id','in',$m->getElement('sale_invoice_ids'));
				return $si_model->count();
			});
			$emp_model->addExpression('total_sale_invoice_amount')->set(function($m,$q){
				$si_model = $m->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'saintotalamount']);
				$si_model->addCondition('id','in',$m->getElement('sale_invoice_ids'));
				return $si_model->sum('net_amount');
			});

			$emp_model->addExpression('sale_invoice_canceled_ids')->set(function($m,$q){
				$so = $m->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>'sisridscan']);
				$so->addCondition('status','Canceled');

				if($this->f_document_date == "only_date_range"){
					$so->addCondition('created_at','>=',$this->from_date);
					$so->addCondition('created_at','<',$this->app->nextDate($this->to_date));
				}
				
				if($this->f_sale_invoice == "created_by"){
					$so->addCondition('created_by_id',$m->getElement('id'));
				}elseif($this->f_sale_invoice == 'based_on_lead'){
					$so->addCondition('contact_id','in',$m->getElement('total_lead_ids'));
				}elseif($this->f_sale_invoice == 'related_to_saleorder'){
					$so->addCondition([
							['related_qsp_master_id','in',$m->getElement('sale_order_ids')],
							['related_qsp_master_id','in',$m->getElement('sale_order_canceled_ids')]
						]);
				}else{
					$so->addCondition([
									['created_by_id',$m->getElement('id')],
									['contact_id','in',$m->getElement('total_lead_ids')],
									['related_qsp_master_id','in',$m->getElement('sale_order_ids')],
									['related_qsp_master_id','in',$m->getElement('sale_order_canceled_ids')]
								]);
				}
				
				if($this->return_sale_order_group_concat)
					return $so->_dsql()->del('fields')->field($q->expr('group_concat([0])',[$so->getElement('id')]));
				return $so->_dsql()->del('fields')->field($q->expr('[0]',[$so->getElement('id')]));
			});

			$emp_model->addExpression('sale_invoice_canceled_details')->set('""');
			// sale invoice cancel reason
			foreach ($this->invoice_cancel_reason as $name) {
				if(!trim($name)) continue;
				$normalize_name = 'sic_'.$this->app->normalizeName($name);

				$this->sale_invoice_cancel_fields[$name] = $normalize_name;
				$this->model_field_array[] = $normalize_name;

				$emp_model->addExpression($normalize_name)->set(function($m,$q)use($name,$normalize_name){
					$soc = $this->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>$normalize_name])
					    ->addCondition('status','Canceled')
					    ->addCondition('cancel_reason',$name)
					    ->addCondition('id','in',$m->getElement('sale_invoice_canceled_ids'))
					    ;
					return $soc->count();
				})->caption($name);

				$normalize_name = $normalize_name."_amount";
				$this->model_field_array[] = $normalize_name;
				// $this->sale_invoice_cancel_fields[$name."_amount"] = $normalize_name;
				$emp_model->addExpression($normalize_name)->set(function($m,$q)use($name,$normalize_name){
					$soc = $this->add('xepan\commerce\Model_SalesInvoice',['table_alias'=>$normalize_name])
					    ->addCondition('status','Canceled')
					    ->addCondition('cancel_reason',$name)
					    ->addCondition('id','in',$m->getElement('sale_invoice_canceled_ids'))
					    ;
					return $q->expr('IFNULL([0],0)',[$soc->sum('net_amount')]);
				})->caption($name." Amount");
			}

			// 'amount': 'based_on_saleinvoice'=>'Based on Sale Invoice','total_lead_balance'=>'Based on lead total balance'
			$emp_model->addExpression('amount_balance')->set(function($m,$q){

				if($this->f_amount == "based_on_saleinvoice"){
					$amt_model = $this->add('xepan\commerce\Model_Lodgement');
					$amt_model->addCondition('invoice_id','in',$m->getElement('sale_invoice_ids'));
					return $q->expr('(IFNULL([0],0)-IFNULL([1],0))',[$m->getElement('total_sale_invoice_amount'),$amt_model->sum('exchange_amount')]);
				}else{
					$amt_model = $this->add('xepan\accounts\Model_Ledger',['table_alias'=>'acctledger']);
					$amt_model->addCondition('contact_id','in',$m->getElement('total_lead_ids'));

					return $q->expr('[0]',[$amt_model->sum('balance_signed')]);
				}
			
			});
		}


		$this->model = $emp_model;
	}


	function page_commlead(){

		$contact = $this->add('xepan\base\Model_Contact',['table_alias'=>'lead_count']);

		if($_GET['show_unique_lead']){
			$emp_model = $this->add('xepan\communication\Model_EmployeeCommunication',['from_date'=>$this->from_date,'to_date'=>$this->to_date]);
			$emp_model->setActualFields(['id','status','unique_leads_from','unique_leads_to','created_by_id','created_at','from_id','to_id']); // for load only pass fields

			$emp_model->addCondition('status','Active');
			$emp_model->addCondition('id',$this->selected_employee_id);			
			$emp_model->from_date = $this->from_date;
			$emp_model->to_date = $this->to_date;

			$emp_model->tryLoadAny();

			$tmp = array_merge(explode(",", $emp_model['unique_leads_from']), explode(",", $emp_model['unique_leads_to']));
			$lead_ids = array_unique($tmp);
			$contact->addCondition('id','in',$lead_ids);
		}else{			
			// work for total lead count for assign, created by or both
			$contact->addCondition([['type','Contact'],['type','Lead'],['type','Customer']]);
			if($this->f_lead_count == "created_by"){
				$contact->addCondition('created_at','>=',$this->from_date)
					->addCondition('created_at','<',$this->api->nextDate($this->to_date))
					->addCondition('created_by_id',$this->selected_employee_id);
			}elseif($this->f_lead_count == "assign_to_emp"){
				$contact->addCondition('assign_at','>=',$this->from_date)
					->addCondition('assign_at','<',$this->api->nextDate($this->to_date))
					->addCondition('assign_to_id',$this->selected_employee_id);
			}else{
				$contact->addCondition([['created_by_id',$this->selected_employee_id],['assign_to_id',$this->selected_employee_id]]);
				$contact->addCondition([
						['created_at','>=',$this->from_date],
						['assign_at','>=',$this->from_date]
					]);
				$contact->addCondition([
						['created_at','<',$this->app->nextDate($this->to_date)],
						['assign_at','<',$this->app->nextDate($this->to_date)]
					]);
			}
		}

		$grid = $this->add('xepan\hr\Grid');
		$grid->setModel($contact,['name_with_type','organization','created_by','assign_to','address','city','source','']);
		$grid->addPaginator(10);

	}

	function page_commsaleorder(){
		$fields = ['sale_order_ids'];
		$field_value = 'sale_order_ids';
		if($this->order_status == "Canceled"){
			$fields = ['sale_order_canceled_ids'];
			$field_value = 'sale_order_canceled_ids';
		}

		$this->return_sale_order_group_concat = true;
		$this->return_sale_invoice_group_concat = false;
		$this->setModel();
		$emp_model = $this->model;
		$emp_model->setActualFields($fields); // for load only pass fields
		$emp_model->tryLoadAny();

		$this->app->filter_sale_order_ids = explode(",", $emp_model[$field_value]);

		$page_obj = $this->add('xepan\commerce\page_salesorder',['crud_options'=>['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false]]);
	}


	function page_commsaleinvoice(){
		$fields = ['sale_invoice_ids'];
		$field_value = 'sale_invoice_ids';
		if($this->invoice_status == "Canceled"){
			$fields = ['sale_invoice_canceled_ids'];
			$field_value = 'sale_invoice_canceled_ids';
		}

		$this->return_sale_order_group_concat = false;
		$this->return_sale_invoice_group_concat = true;
		$this->setModel();
		$emp_model = $this->model;
		$emp_model->setActualFields($fields); // for load only pass fields
		$emp_model->tryLoadAny();

		$this->app->filter_sale_invoice_ids = explode(",", $emp_model[$field_value]);
		$page_obj = $this->add('xepan\commerce\page_salesinvoice',['crud_options'=>['allow_add'=>false,'allow_edit'=>false,'allow_del'=>false]]);
	}

}