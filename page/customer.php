<?php
 
namespace xepan\commerce;

class page_customer extends \Page {
	public $title='Customers';

	function init(){
		parent::init();

		$customer_model = $this->add('xepan\commerce\Model_Customer');

		//Total Orders
		$customer_model->addExpression('orders')->set(" 'Todo 10' ");

		$crud = $this->add('xepan\hr\CRUD',
							['action_page'=>'xepan_commerce_customerdetail'],
							null,
							['view/customer/grid']
						);

		$crud->setModel($customer_model);
		// $crud->grid->addPaginator(10);

		$frm=$crud->grid->addQuickSearch(['name']);
	
		$frm_drop=$frm->addField('DropDown','status')->setValueList(['Active'=>'Active','Inactive'=>'Inactive'])->setEmptyText('Status');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['customer_id'])
				$m->addCondition('category_id',$frm['customer_id']);
			
			if($frm['status']='Active'){
				$m->addCondition('status','Active');
			}else{
				$m->addCondition('status','Inactive');

			}

		});
		
		$crud->add('xepan\base\Controller_Avatar');

	}
}



























// <?php
//  namespace xepan\commerce;
//  class page_customerprofile extends \Page{
//  	public $title='Customer';

// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/customerprofile'];
// 	}
// }