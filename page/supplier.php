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
		$crud->grid->addPaginator(10);
		$frm=$crud->grid->addQuickSearch(['name']);
		

		$frm_drop =$frm->addField('DropDown','status')->setEmptyText("Select status");
		$frm_drop->setModel('xepan\commerce\Supplier');
		$frm_drop->js('change',$frm->js()->submit());

		$frm_drop=$frm->addField('DropDown','status')->setValueList(['Active'=>'Active','Inactive'=>'Inactive'])->setEmptyText('Status');
		$frm_drop->js('change',$frm->js()->submit());

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