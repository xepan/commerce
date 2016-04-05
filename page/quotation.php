<?php 
 namespace xepan\commerce;
 class page_quotation extends \Page{

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

		$crud->setModel($quotation);
		$crud->grid->addPaginator(10);
		$frm=$crud->grid->addQuickSearch(['document_no','contact']);
		

		$frm_drop=$frm->addField('DropDown','display_type')->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Approved'=>'Approved','Redesign'=>'Redesign','Rejected'=>'Rejected','Converted'=>'Converted'])->setEmptyText('display_type');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['quotation_id'])
				$m->addCondition('quotation_id',$frm['quotation_id']);
		});

		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
	}

} 