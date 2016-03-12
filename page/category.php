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
		$crud->grid->addQuickSearch(['name']);

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