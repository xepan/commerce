<?php 
 namespace xepan\commerce;
 class page_quotation extends \xepan\base\Page{

	public $title='Quotations';

	function init(){
		parent::init();

		$quotation = $this->add('xepan\commerce\Model_Quotation');
		$quotation->add('xepan\commerce\Controller_SideBarStatusFilter');

		$quotation->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});

		$quotation->addExpression('contact_type',$quotation->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_quotationdetail']
						,null,
						['view/quotation/grid']);

		$crud->grid->addHook('formatRow',function($g){
			switch($g->model['contact_type']){
				case 'Lead':
					$contact_url='xepan_marketing_leaddetails';
					break;
				case 'Customer':
					$contact_url='xepan_commerce_customerdetail';
					break;
				case 'Supplier':
					$contact_url='xepan_commerce_supplierdetail';
					break;
				case 'Employee':
					$contact_url='xepan_hr_employeedetail';
					break;
				default:
					$contact_url='xepan_base_contactdetail';
			}
			$g->current_row['contact_url']= $contact_url;
		});

		$crud->setModel($quotation)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		$frm=$crud->grid->addQuickSearch(['document_no','contact']);

		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Quotation Details',[$this->api->url('xepan_commerce_quotationdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-quotation-id]')->data('id')]);
			$crud->grid->js('click')->_selector('.do-view-customer-frame')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
		}
	}

} 