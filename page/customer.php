<?php
 
namespace xepan\commerce;

class page_customer extends \xepan\base\Page {
	public $title='Customers';

	function init(){
		parent::init();

		$customer_model = $this->add('xepan\commerce\Model_Customer');
		$customer_model->add('xepan\commerce\Controller_SideBarStatusFilter');
		

		//Total Orders
		$customer_model->addExpression('orders')->set(" 'Todo 10' ");

		$crud = $this->add('xepan\hr\CRUD',
							['action_page'=>'xepan_commerce_customerdetail'],
							null,
							['view/customer/grid']
						);

		$crud->setModel($customer_model)->setOrder('created_at','desc');
		$crud->grid->addPaginator(50);

		$frm=$crud->grid->addQuickSearch(['name']);
		
		$crud->add('xepan\base\Controller_Avatar');

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Customer Details',[$this->api->url('xepan_commerce_customerdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-customer-id]')->data('id')]);
		}
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