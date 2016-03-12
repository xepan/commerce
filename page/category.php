<?php
 
namespace xepan\commerce;

class page_category extends \Page {
	public $title='Category';

	function init(){
		parent::init();

		$category_model = $this->add('xepan\commerce\Model_Category');

		$crud = $this->add('xepan\hr\CRUD',
							null,
							null,
							['view/item/category']
						);

		$crud->setModel($category_model);
		$crud->grid->addPaginator(10);
		$crud->add('xepan\base\Controller_Avatar');

		$frm=$crud->grid->addQuickSearch(['name']);
		$frm_drop =$frm->addField('DropDown','status')->setEmptyText("Select status");
		$frm_drop->setModel('xepan\commerce\Category');
		$frm_drop->js('change',$frm->js()->submit());

		$frm_drop=$frm->addField('DropDown','status')->setValueList(['Active'=>'Active','Inactive'=>'Inactive'])->setEmptyText('Status');
		$frm_drop->js('change',$frm->js()->submit());



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