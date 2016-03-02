<?php
 
/**
* description: Customer
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\commerce;

class page_supplier extends \Page {
	public $title='Supplier';

	function init(){
		parent::init();
		
		$supplier=$this->add('xepan\commerce\Model_Supplier');

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_supplierdetail'],
						null,
						['view\supplier\grid']
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