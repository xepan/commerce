<?php 
 namespace xepan\commerce;
 class page_quotation extends \xepan\base\Page{

	public $title='Quotations';

	function init(){
		parent::init();

		$quotation = $this->add('xepan\commerce\Model_Quotation');
		$quotation->add('xepan\base\Controller_TopBarStatusFilter');

		$quotation->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});

		$quotation->addExpression('contact_type',$quotation->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_quickqsp&document_type=Quotation']
						,null,
						['view/quotation/grid']);

		$crud->grid->addHook('formatRow',function($g){
			switch($g->model['contact_type']){
				case 'Lead':
					$contact_type = 'Lead';
					$contact_url='xepan_marketing_leaddetails'.'&contact_id='.$g->model['contact_id'];
					break;
				case 'Customer':
					$contact_type = 'Customer';
					$contact_url='xepan_commerce_customerdetail'.'&contact_id='.$g->model['contact_id'];
					break;
				case 'Supplier':
					$contact_type = 'Supplier';
					$contact_url='xepan_commerce_supplierdetail'.'&contact_id='.$g->model['contact_id'];
					break;
				case 'Employee':
					$contact_type = 'Employee';
					$contact_url='xepan_hr_employeedetail'.'&contact_id='.$g->model['contact_id'];
					break;
				default:
					$contact_type = 'Contact';
					$contact_url='xepan_base_contactdetail'.'&contact_id='.$g->model['contact_id'];
			}
			$g->current_row['contact_url']= $contact_url;
			$g->current_row_html['contact_detail_name']= $contact_type;

			$other_data = array_intersect_key($g->model->data,$g->model->otherInfoFields);
			if(count($other_data))
				$g->current_row_html['other_info'] = trim(trim(str_replace(",", "<br/>",json_encode($other_data)),'{'),'}');
			else
				$g->current_row_html['other_info'] = "-";
		});


		$crud->setModel($quotation)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$frm = $crud->grid->addQuickSearch(array_merge(['document_no','contact','net_amount'],$quotation->otherInfoFields));

		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
		$crud->add('xepan\base\Controller_MultiDelete');
		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Quotation Details',[$this->api->url('xepan_commerce_quickqsp'),'document_id'=>$this->js()->_selectorThis()->closest('[data-quotation-id]')->data('id'),'readmode'=>1]);
		}
	}

} 