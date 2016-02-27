<?php
 
namespace xepan\commerce;

class page_customer extends \Page {
	public $title='Customer';

	function init(){
		parent::init();

		$customer=$this->add('xepan\commerce\Model_Customer');

		$crud=$this->add('xepan\hr\CRUD',['action_page'=>'xepan_commerce_customerdetail'],null,['view/customer/grid']);

		$crud->setModel($customer);
		$crud->grid->addQuickSearch(['name']);

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