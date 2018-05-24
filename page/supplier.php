<?php
 
/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\commerce;

class page_supplier extends \xepan\base\Page {
	public $title='Suppliers';

	function init(){
		parent::init();
		
		$supplier=$this->add('xepan\commerce\Model_Supplier');
		$supplier->add('xepan\base\Controller_TopBarStatusFilter');
		
		$supplier->addExpression('organization_name_with_name')
					->set($supplier->dsql()
						->expr('CONCAT(IFNULL([0],"")," ::[ ",IFNULL([1],"")," ]")',
							[$supplier->getElement('name'),
								$supplier->getElement('organization')]))
					->sortable(true);

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_supplierdetail'],
						null,
						['view\supplier\grid']
					);


		$crud->setModel($supplier)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);
		
		$frm=$crud->grid->addQuickSearch(['name']);
		
		$crud->add('xepan\base\Controller_Avatar');
		$crud->add('xepan\base\Controller_MultiDelete');
		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-supplier-detail')->univ()->frameURL('Supplier Details',[$this->api->url('xepan_commerce_supplierdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-supplier-id]')->data('id')]);
		}
	}
}