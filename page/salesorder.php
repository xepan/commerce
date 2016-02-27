<?php
  namespace xepan\commerce;
  class page_salesorder extends \Page{

 	public $title='Sales Order';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/salesorder'];
	}
}










// <?php

// namespace xepan\commerce;

// class page_salesorder extends \Page {
// 	public $title='SalesOrder';

// 	function init(){
// 		parent::init();

		
// 		$supplier=$this->add('xepan\commerce\Model_SalesOrder');

// 		$crud=$this->add('xepan\base\CRUD',
// 			[
// 				'grid_options'=>
// 					[
// 						'defaultTemplate'=>['grid/salesorder']
// 					]
// 			]
// 		);

// 		$crud->setModel($salesorder);
// 		//$crud->grid->addQuickSearch(['name']);
// 	}
// }

