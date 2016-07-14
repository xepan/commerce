<?php 
 namespace xepan\commerce;
 class page_discountvoucher extends \xepan\base\Page{

	public $title='Discount Vouchers';

	function init(){
		parent::init();

		$discount = $this->add('xepan\commerce\Model_DiscountVoucher');
		$crud = $this->add('xepan\hr\CRUD',null,null,['view/discount/vouchers/grid']);

		$crud->setModel($discount);


		$crud->grid->add('VirtualPage')
			->addColumn('VoucherCondition','Vouchers Condition ',['descr'=>'VoucherCondition'])
			->set(function($page){
				$form_id = $_GET[$page->short_name.'_id'];

				$condition_model = $page->add('xepan\commerce\Model_DiscountVoucherCondition')->addCondition('discountvoucher_id',$form_id);

				$crud_field = $page->add('xepan\hr\CRUD',null,null,['view/discount/vouchers/condition-grid']);
				$crud_field->setModel($condition_model);
				$crud_field->grid->addQuickSearch(['name','from','to']);
				$crud_field->grid->addPaginator(10);

		});
		// $crud->addRef('xepan/commerce/DiscountVoucherCondition');
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
	}

}  