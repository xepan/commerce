<?php 
 namespace xepan\commerce;
 class page_discountvoucher extends \xepan\base\Page{

	public $title='Discount Vouchers';

	function init(){
		parent::init();

		$discount = $this->add('xepan\commerce\Model_DiscountVoucher');
		$crud = $this->add('xepan\hr\CRUD',null,null,['view/discount/vouchers/grid']);
		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->layout([
						'name~Voucher Number'=>'Discount Voucher~c1~3',
						'start_date'=>'c2~3',
						'expire_date'=>'c3~3',
						'status'=>'c4~3',
						'on_category_id~On Category'=>'c11~6',
						'include_sub_category'=>'c12~6',
						'no_of_person'=>'c11~6~How many person ? (i.e. customer for online purchasing)',
						'one_user_how_many_time'=>'c12~6~How many time it can be used by one customer during online purchasing ?',
						'on'=>'c11~6',
						'based_on'=>'c12~6',
						'FormButtons~&nbsp'=>'c11~6'
					]);
		}

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
				$crud_field->add('xepan\base\Controller_MultiDelete');

		});
		// $crud->addRef('xepan/commerce/DiscountVoucherCondition');
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(25);
		$crud->add('xepan\base\Controller_MultiDelete');
	}

}  