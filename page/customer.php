<?php

namespace xepan\commerce;

class page_customer extends \Page {
	public $title='Customer';

	function init(){
		parent::init();

		
		$customer=$this->add('xepan\commerce\Model_Customer');

		$crud=$this->add('xepan\base\CRUD',
			[
				'grid_options'=>
					[
						'defaultTemplate'=>['grid/customer']
					]
			]
		);

		$crud->setModel($customer);
		//$crud->grid->addQuickSearch(['name']);
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