<?php
 
namespace xepan\commerce;

class page_customer extends \Page {
	public $title='Customer';

	function init(){
		parent::init();

		//$this->api->stickyGET('post_id');
		
		$customer=$this->add('xepan\commerce\Model_Customer');

		// if($_GET['post_id']){
		// 	$employee->addCondition('post_id',$_GET['post_id']);
		// }

		$crud=$this->add('xepan\base\CRUD',
			[
				'action_page'=>'xepan_commerce_customerdetail',
				'grid_options'=>
					[
						'defaultTemplate'=>['grid/customer']
					]
			]
		);

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