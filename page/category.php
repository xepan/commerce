<?php
 
namespace xepan\commerce;

class page_category extends \xepan\base\Page {
	public $title='Category';

	function init(){
		parent::init();

		$category_model = $this->add('xepan\commerce\Model_Category');
		$category_model->add('xepan\commerce\Controller_SideBarStatusFilter');
		
		$crud = $this->add('xepan\hr\CRUD',
							null,
							null,
							['view/item/category']
						);

		if($crud->isEditing()){
			$crud->form->setLayout('view\form\category');
		}

		$crud->setModel($category_model);
		$crud->grid->addPaginator(50);
		$crud->add('xepan\base\Controller_Avatar');

		$frm=$crud->grid->addQuickSearch(['name']);
		
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