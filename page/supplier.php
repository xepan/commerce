<?php
 
/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\commerce;

class page_supplier extends \Page {
	public $title='Suppliers';

	function init(){
		parent::init();
		
		$supplier=$this->add('xepan\commerce\Model_Supplier');

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_supplierdetail'],
						null,
						['view\supplier\grid']
					);

		$crud->setModel($supplier);
		$crud->grid->addPaginator(10);
		$frm=$crud->grid->addQuickSearch(['name']);
		
		$frm_drop=$frm->addField('DropDown','status')->setValueList(['Active'=>'Active','Inactive'=>'Inactive'])->setEmptyText('Status');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['category_id'])
				$m->addCondition('supplier_id',$frm['supplier_id']);
			
			if($frm['status']='Active'){
				$m->addCondition('status','Active');
			}else{
				$m->addCondition('status','Inactive');

			}

		});

		$crud->add('xepan\base\Controller_Avatar');

	}
}