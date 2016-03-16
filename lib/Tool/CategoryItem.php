<?php

namespace xepan\commerce;

class Tool_CategoryItem extends \xepan\base\View_Tool{
	public $options = [
					'show_category'=>true,/* true, false*/ 
					'is_searchable'=>'1',
					'category'=>"sushil"
				];

	function init(){
		parent::init();

		// $category_id = $this->app->stickyGET('commerce_category_id');
		$category = $this->add('xepan\commerce\Model_Category')->tryLoadAny();
		if(!$category->loaded())
			throw $this->exception('Category not found');
		$this->setModel($category);

	}

	function defaultTemplate(){
		return ['view/item/categoryitem'];
	}
}