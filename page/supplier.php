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
		$supplier->add('xepan\commerce\Controller_SideBarStatusFilter');
		

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_supplierdetail'],
						null,
						['view\supplier\grid']
					);

		$crud->setModel($supplier);
		$crud->grid->addPaginator(10);
		$frm=$crud->grid->addQuickSearch(['name']);
		
		$crud->add('xepan\base\Controller_Avatar');

	}
}