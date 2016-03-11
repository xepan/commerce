<?php 
 namespace xepan\commerce;
 class page_quotation extends \Page{

	public $title='Quotations';

	function init(){
		parent::init();

		$quotation = $this->add('xepan\commerce\Model_Quotation');
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
		$crud->grid->addQuickSearch(['document_no','contact']);

	}

}  