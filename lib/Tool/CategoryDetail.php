<?php

namespace xepan\commerce;

class Tool_CategoryDetail extends \xepan\cms\View_Tool{
	public $options = [
				'show_name'=>true,
				'show_image'=>true,
				'show_price' =>true,
				'show_category_description'=>true,
				'show_item_count' =>true,
				'show_childs_item_count'=>true
			];

	function init(){
		parent::init();

		$category = $this->add('xepan\commerce\Model_Category')->addCondition('id',$_GET['category_id']);
		$category->tryLoadAny();

		if(!$category->loaded()){
			$this->add('View_Error')->set('Category not found');
			$this->template->del('counts');
			return;
		}

		$this->add('xepan\cms\Controller_Tool_Optionhelper',['model'=>$category]);

		$this->setModel($category);

		$this->template->setHtml('category_description',$this->model['description']);
	}

	function defaultTemplate(){
		return ['view\tool\categorydetail'];
	}
}