<?php
 
namespace xepan\commerce;

class page_supplier extends \Page {
	public $title='Supplier';

	function init(){
		parent::init();

		//$this->api->stickyGET('post_id');
		
		$supplier=$this->add('xepan\commerce\Model_Supplier');

		// if($_GET['post_id']){
		// 	$employee->addCondition('post_id',$_GET['post_id']);
		// }

		$crud=$this->add('xepan\base\CRUD',
			[
				'action_page'=>'xepan_commerce_supplierdetail',
				'grid_options'=>
					[
						'defaultTemplate'=>['grid/supplier']
					]
			]
		);

		$crud->setModel($supplier);
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
// }// <?php
//  namespace xepan\commerce;
//  class page_supplier extends \Page{

//  	public $title='Supplier';


// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/supplier'];
// 	}
// }