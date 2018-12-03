<?php

 namespace xepan\commerce;
 class page_salesinvoice extends \xepan\base\Page{

	public $title='Sales Invoices';
	public $invoice_model = "xepan\commerce\Model_SalesInvoice";
	public $crud;
	public $filter_form;
	public $crud_options =[];
	function init(){
		parent::init();
		
		// datetime used in widget monthlysalesinvoice
		$this->app->stickyGET('status');
		$monthyear = $this->app->stickyGET('monthyear');
		$customer_id = $this->app->stickyGET('customer_id');
		
		$filter = $this->app->stickyGET('filter');
		$from_date = $this->app->stickyGET('f_from_date');
		$to_date = $this->app->stickyGET('f_to_date');
		$customer = $this->app->stickyGET('f_customer');
		$created_by_employee = $this->app->stickyGET('f_created_by_employee');
		$branch = $this->app->stickyGET('f_branch');
		$status = $this->app->stickyGET('f_status');
		$city = $this->app->stickyGET('f_city');

		
		$salesinvoice = $this->add($this->invoice_model);
		$salesinvoice->add('xepan\base\Controller_TopBarStatusFilter');
		$salesinvoice->addExpression('city')->set(function($m,$q){
			return $q->expr('[0]',[$m->refSQL('contact_id')->fieldQuery('city')]);
		});

		// filter form
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->showLables(true)
			->makePanelsCoppalsible(true)
			->addContentSpot()
			->layout([
					'from_date'=>'Filter~c1~2~closed',
					'to_date'=>'c2~2',
					'created_by_employee'=>'c3~2',
					'branch'=>'c4~2',
					'customer'=>'c5~2',
					'status'=>'c6~2',
					'city'=>'c7~2',
					'FormButtons~&nbsp;'=>'c12~2'
				]);

		$field_from_date = $form->addField('DatePicker','from_date');
		$field_to_date = $form->addField('DatePicker','to_date');
		$field_created_by_emp = $form->addField('xepan\hr\Employee','created_by_employee');

		$field_branch = $form->addField('DropDown','branch');
		$field_branch->setModel('xepan\base\Branch');
		$field_branch->setEmptyText('All');

		$field_customer = $form->addField('DropDown','customer');
		$field_customer->setModel('xepan\commerce\customer');
		$field_customer->setEmptyText('All');

		$field_status = $form->addField('DropDown','status');
		$field_status->setValueList(array_combine($salesinvoice->status, $salesinvoice->status));
		$field_status->setEmptyText('All');
			

		$data = $this->app->db->dsql()->expr('SELECT DISTINCT(city) AS city FROM contact')->get();
		$city_list = [];
		foreach ($data as $key => $value) {
			if(!trim($value['city'])) continue;
			$city_list[$value['city']] = $value['city'];
		}

		$field_city = $form->addField('DropDown','city');
		$field_city->setValueList($city_list);
		$field_city->setEmptyText('All');
	
		$form->addSubmit('Filter');

		// FOR WIDGET MONTHLY INVOICES		
		if($monthyear){
			$salesinvoice->addExpression('monthyear')->set('DATE_FORMAT(created_at,"%M %Y")');
			$salesinvoice->addCondition('monthyear',$monthyear);
		}

		if($customer_id)
			$salesinvoice->addCondition('contact_id',$customer_id);

		$salesinvoice->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});


		$salesinvoice->addExpression('contact_type',$salesinvoice->refSQL('contact_id')->fieldQuery('type'));

		$salesinvoice->addExpression('contact_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('name');
		});
		$salesinvoice->addExpression('contact_organization_name',function($m,$q){
			return $m->refSQL('contact_id')->fieldQuery('organization');
		});

		$salesinvoice->addExpression('organization_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization_name]) OR trim([organization_name])="" ,[contact_name],[organization_name])',
						[
							'contact_name'=>$m->getElement('contact_name'),
							'organization_name'=>$m->getElement('contact_organization_name')
						]
					);
		});

		$salesinvoice->addExpression('ord_no',function($m,$q){
			return $m->refSQL('related_qsp_master_id')->fieldQuery('document_no');
		});

		$salesinvoice->addExpression('sales_order_id',function($m,$q){
			return $m->refSQL('related_qsp_master_id')->fieldQuery('id');
		});

		if(isset($this->app->filter_sale_invoice_ids) and count($this->app->filter_sale_invoice_ids))
			$salesinvoice->addCondition('id',$this->app->filter_sale_invoice_ids);

		// filter apply here
		if($filter){
			if($from_date)
				$salesinvoice->addCondition('created_at','>=',$from_date);
			if($to_date)
				$salesinvoice->addCondition('created_at','<',$this->app->nextDate($to_date));
			if($customer)
				$salesinvoice->addCondition('contact_id',$customer);
			if($created_by_employee)
				$salesinvoice->addCondition('created_by_id',$created_by_employee);
			if($branch)
				$salesinvoice->addCondition('branch_id',$branch);
			if($status)
				$salesinvoice->addCondition('status',$status);
			if($city)
				$salesinvoice->addCondition('city',$city);
		}

		$this->crud = $crud=$this->add('xepan\hr\CRUD',
						array_merge(['action_page'=>'xepan_commerce_quickqsp&document_type=SalesInvoice'],$this->crud_options)
						,null,
						['view/invoice/sale/grid']);

		$salesinvoice->setOrder('created_at','DESC');
		$crud->grid->addColumn('other_info');
		$crud->grid->addHook('formatRow',function($g){
			$other_data = array_intersect_key($g->model->data,$g->model->otherInfoFields);
			if(count($other_data))
				$g->current_row_html['other_info'] = trim(trim(str_replace(",", "<br/>",json_encode($other_data)),'{'),'}');
			else
				$g->current_row_html['other_info'] = "-";

			if($g->model['branch_id'])
				$g->current_row_html['branch_data'] = "Branch: ".$g->model['branch'];
			else
				$g->current_row_html['branch_data'] = " ";

		});

		$crud->setModel($salesinvoice)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$this->filter_form = $frm = $crud->grid->addQuickSearch(array_merge(['contact_name','organization_name','document_no','net_amount_self_currency','serial'],$salesinvoice->otherInfoFields));
		
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
		$crud->add('xepan\base\Controller_MultiDelete');

		// $qpos_btn = $crud->grid->add('Button',null,'grid_buttons')->set('Quick POS')->addClass('btn btn-success pull-right qps_pos_btn');

		// if($qpos_btn->isClicked()){
		// 	// $this->js()->univ()->frameURL('Quick POS',$this->api->url('xepan_commerce_quickpos'));
		// 	$this->js()->univ()->newWindow($this->api->url('xepan_commerce_quickpos'),'Quick POS')->execute();
		// }

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Sales Invoice Details',[$this->api->url('xepan_commerce_quickqsp'),'document_id'=>$this->js()->_selectorThis()->closest('[data-salesinvoice-id]')->data('id'),'readmode'=>1]);
			$crud->grid->js('click')->_selector('.do-view-customer-frame')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
			$crud->grid->js('click')->_selector('.order-invoice-number')->univ()->frameURL('Order Detail',[$this->api->url('xepan_commerce_quickqsp'),'document_id'=>$this->js()->_selectorThis()->data('salesorder-id'),'readmode'=>1]);
		}


		if($form->isSubmitted()){			
			$form->js(null,$crud->js()->reload([
				'filter'=>1,
				'f_from_date'=>$form['from_date']?:0,
				'f_to_date'=>$form['to_date']?:0,
				'f_created_by_employee'=>$form['created_by_employee']?:0,
				'f_branch'=>$form['branch']?:0,
				'f_customer'=>$form['customer']?:0,
				'f_status'=>$form['status']?:0,
				'f_city'=>$form['city']?:0,
			]))->execute();

		}

	}
} 
