<?php

namespace xepan\commerce;

class page_reports_salesreport extends \xepan\base\Page{

	public $title = "Sales Report";
	public $sub_type_1_fields;
	public $sub_type_1_norm_unnorm_array=[];
	public $sub_type_2_fields;
	public $sub_type_2_norm_unnorm_array=[];
	public $sub_type_3_fields;
	public $sub_type_3_norm_unnorm_array=[];
	public $communication_fields;
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

	function init(){
		parent::init();

		// sticky get param
		$this->emp_id = $this->app->stickyGET('employee_id');
		$this->from_date = $from_date  = $this->app->stickyGET('from_date')?:$this->app->today;
		$this->to_date = $to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$this->department_id = $department = $this->app->stickyGET('department');
		$this->f_lead_count = $this->app->stickyGET('f_lead_count')?:"both";
		$this->f_document_date = $this->app->stickyGET('f_document_date')?:"all";
		$this->f_sale_order = $this->app->stickyGET('f_sale_order')?:"all";
		$this->f_sale_invoice = $this->app->stickyGET('f_sale_invoice')?:"all";
		$this->f_amount = $this->app->stickyGET('f_amount')?:"based_on_saleinvoice";

		
		$this->config_m = $this->add('xepan\communication\Model_Config_SubType');
		$this->config_m->tryLoadAny();

		// subtype 1
		foreach(explode(",", $this->config_m['sub_type']) as $subtypes) {
			$subtype_name = $this->app->normalizeName($subtypes);
			$this->sub_type_1_norm_unnorm_array[$subtype_name] = $subtypes;
		}

		foreach(explode(",", $this->config_m['calling_status']) as $subtypes) {
			$subtype_name = $this->app->normalizeName($subtypes);
			$this->sub_type_2_norm_unnorm_array[$subtype_name] = $subtypes;
		}

		foreach(explode(",", $this->config_m['sub_type_3']) as $subtypes) {
			$subtype_name = $this->app->normalizeName($subtypes);
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
					'date_range'=>'Filter~c1~4',
					'employee'=>'c2~4',
					'department'=>'c3~4',
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
			$emp_field->setModel($employee_model);
					
			$dept_field = $form->addField('xepan\base\DropDown','department');
			$dept_field->setModel('xepan\hr\Model_Department');
			$dept_field->setEmptyText('All');

			$lead_count_field = $form->addField('DropDown','lead_count')->setValueList(['created_by'=>'Created By Employee','assign_to_emp'=>'Assign to Employee','both'=>'Both'])->set($this->f_lead_count);
			$document_date_field = $form->addField('DropDown','document_date')->setValueList(['only_date_range'=>'Apply Date','all'=>'All'])->set($this->f_document_date);
			$sale_order_field = $form->addField('DropDown','sale_order')->setValueList(['based_on_lead'=>'Based on lead count','created_by'=>'Created By Self','all'=>'All'])->set($this->f_sale_order);
			$sale_invoice_field = $form->addField('DropDown','sale_invoice')->setValueList(['based_on_lead'=>'Based on lead count','created_by'=>'Created By Self','related_to_saleorder'=>'Related to Sale Order','all'=>'All'])->set($this->f_sale_invoice);
			$amount = $form->addField('DropDown','amount')->setValueList(['based_on_saleinvoice'=>'Based on Sale Invoice','total_lead_balance'=>'Based on lead total balance'])->set($this->f_amount);

			$form->addSubmit('Get Details')->addClass('btn btn-primary');
		}
		
		$this->setModel();
		// grid
		$grid = $this->add('xepan\hr\Grid');

		$grid->setModel($this->model->debug(),$this->model_field_array);
		$order = $grid->addOrder();
		$grid->addpaginator(10);
		$grid->template->tryDel('Pannel');
		

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
							'f_amount'=>$form['amount']
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
			$g->current_row_html['unique_lead'] = $unique_lead_count;

			// $l_ids = array_unique(explode(",", $g->model['total_lead_ids']));
			// $l_ids_count = count($l_ids);
			// if($l_ids_count == 1 && !$l_ids[0]){
			// 	$l_ids_count = 0;
			// }
			// $g->current_row_html['total_lead_ids'] = $l_ids_count;

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
			

			$g->current_row_html['communication'] = '<div class="row""><div class="col-md-7 col-xs-12 col-lg-7 col-sm-12"> <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline communication"></div></div><div class="col-md-5 col-xs-12 col-lg-5 col-sm-12"> <small>'.$comm_label_str."</small></div></div>";
			$g->current_row_html['subtype_1'] = '<div class="row"><div class="col-md-7 col-xs-12 col-lg-7 col-sm-12"> <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline subtype1"></div></div><div class="col-md-5 col-xs-12 col-lg-5 col-sm-12"><small>'.$sub_type_1_label_str."</small></div></div>";
			$g->current_row_html['subtype_2'] = '<div class="row"><div  class="col-md-7 col-xs-12 col-lg-7 col-sm-12"> <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline subtype2"></div></div><div class="col-md-5 col-xs-12 col-lg-5 col-sm-12"><small>'.$sub_type_2_label_str."</small></div></div>";
			$g->current_row_html['subtype_3'] = '<div class="row"><div class="col-md-7 col-xs-12 col-lg-7 col-sm-12"> <div data-id="'.$g->model->id.'" sparkType="pie" sparkHeight="70px" class="sparkline subtype3"></div></div><div class="col-md-5 col-xs-12 col-lg-5 col-sm-12"><small>'.$sub_type_3_label_str."</small></div></div>";

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

		if($communication_type)
			$comm_model->addCondition('communication_type',$communication_type);

		if($sub_type_1)
			$comm_model->addCondition('sub_type',$this->sub_type_1_norm_unnorm_array[$sub_type_1]);
		if($sub_type_2)
			$comm_model->addCondition('calling_status',$this->sub_type_2_norm_unnorm_array[$sub_type_2]);
		if($sub_type_3)
			$comm_model->addCondition('sub_type_3',$this->sub_type_3_norm_unnorm_array[$sub_type_3]);

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
		}
		if($this->from_date){
			$emp_model->from_date = $this->from_date;
		}
		if($this->to_date){
			$emp_model->to_date = $this->to_date;
		}
		if($this->department_id){
			$emp_model->addCondition('department_id',$this->department_id);
		}

		$this->communication_fields = ['total_email','total_comment','total_meeting','total_sms','total_telemarketing','total_call','dial_call','received_call'];
		/*Communication Sub Type Form */
		$this->model_field_array = ['name','total_lead_count','unique_lead','communication','total_email','total_comment','total_meeting','total_sms','total_telemarketing','total_call','dial_call','received_call','unique_leads_from','unique_leads_to','sale_order_detail','total_sale_invoice','total_sale_invoice_amount','amount_balance'];

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
		$emp_model->addExpression('communication')->set('""');

		// sub type 1
		$emp_model->addExpression('subtype_1')->set('""')->caption($this->config_m['sub_type_1_label_name']?:"Sub Type 1");
		$this->model_field_array[] = "subtype_1";
		foreach (explode(",", $this->config_m['sub_type']) as $subtypes) {
			// $grid->addColumn($this->app->normalizeName($subtypes));
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
			$subtype_name = $this->app->normalizeName($sub_type_3);
			$this->model_field_array[] = $subtype_name;
			$this->sub_type_3_fields[] = $subtype_name;
			$emp_model->addExpression($subtype_name)->set(function($m,$q)use($sub_type_3){
					return $m->add('xepan\communication\Model_Communication')
								->addCondition('created_by_id',$q->getfield('id'))
								->addCondition('calling_status',$sub_type_3)
								->addCondition('created_at','>=',$this->from_date)
								->addCondition('created_at','<',$this->api->nextDate($this->to_date))
								->count();
				});
		}


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
			return $q->expr('CONCAT("Total Order: ",IFNULL([0],0),", Total Amount: ",IFNULL([1],0))',[$so->count(),$so->sum('net_amount')]);
		});

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

		$this->model = $emp_model;
	}


}