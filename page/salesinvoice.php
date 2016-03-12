<?php 
 namespace xepan\commerce;
 class page_salesinvoice extends \Page{

	public $title='Sales Invoice';

	function init(){
		parent::init();

		$salesinvoice = $this->add('xepan\commerce\Model_SalesInvoice');
		$salesinvoice->addExpression('contact_type',$salesinvoice->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesinvoicedetail']
						,null,
						['view/invoice/sale/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});

		$crud->setModel($salesinvoice);
		$crud->grid->addPaginator(10);
		$frm=$crud->grid->addQuickSearch(['name']);
		
		$frm_drop=$frm->addField('DropDown','Actions')->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Approved'=>'Approved','Redesign'=>'Redesign','Rejected'=>'Rejected','Converted'=>'Converted'])->setEmptyText('Actions');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['salesinvoice_id'])
				$m->addCondition('salesinvoice_id',$frm['salesinvoice_id']);
		});
		}
} 