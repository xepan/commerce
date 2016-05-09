<?php 
 namespace xepan\commerce;
 class page_discountvoucher extends \xepan\base\Page{

	public $title='Discount Vouchers';

	function init(){
		parent::init();

		$discount = $this->add('xepan\commerce\Model_DiscountVoucher');
		$crud=$this->add('xepan\hr\CRUD',null,
						null,
						['view/discount/vouchers/grid']
					);

		$crud->setModel($discount);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);

		$crud->grid->add('VirtualPage')
		->addColumn('used')
		->set(function($page){
		    $id = $_GET[$page->short_name.'_id'];
		    $used = $page->add('xepan\commerce\Model_DiscountVoucherUsed');
		    $used->addCondition('discountvoucher_id',$id);
		    $crud = $page->add('xepan\base\CRUD');
		    $crud->setModel($used);
		});
		
	}

}  