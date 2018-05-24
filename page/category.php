<?php
 
namespace xepan\commerce;

class page_category extends \xepan\base\Page {
	public $title='Category';

	function init(){
		parent::init();

		$category_model = $this->add('xepan\commerce\Model_Category');
		$category_model->add('xepan\base\Controller_TopBarStatusFilter');
		
		$crud = $this->add('xepan\hr\CRUD',
							null,
							null,
							['view/item/category']
						);

		$crud->form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout([
					'name'=>'Category Detail~c1~5',
					'parent_category_id~Parent Category'=>'c2~5',
					'display_sequence'=>'c3~2',
					'description'=>'c4~12~redirect page with category slug url- <strong>{{ url }}</strong>, category id - <strong>{{ category_id }}</strong> and remove all space.',
					'cat_image_id'=>'c6~6',
					'custom_link'=>'c5~6',

					'meta_title'=>'SEO Info~c8~4',
					'meta_keywords'=>'c8~4',
					'alt_text'=>'c8~4~alt text for image',
					'meta_description'=>'c9~4',
					'is_website_display~'=>'c12~4',
					'slug_url'=>'c12~4',
				]);

		// if($crud->isEditing()){
			// $crud->form->setLayout('view\form\category');
		// }

		$crud->setModel($category_model);
		$crud->grid->addPaginator(50);
		$crud->add('xepan\base\Controller_Avatar');
		$crud->add('xepan\base\Controller_MultiDelete');
		
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