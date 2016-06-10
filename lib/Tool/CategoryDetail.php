<?php

namespace xepan\commerce;

class Tool_CategoryDetail extends \xepan\cms\View_Tool{
	public $options = [
				'show_name'=>true,
				'show_image'=>true,
				'show_price' =>true,
				'show_description'=>true,
				'show_item_count' =>true,
				'include_child_category'=>true,
				'redirect_page'=>'index'
			];

	function init(){
		parent::init();

		$category = $this->add('xepan\commerce\Model_Category')->addCondition('id',$_GET['xsnb_category_id']);
		$category->tryLoadAny();

		if(!$category->loaded()){
			$this->add('View_Error')->set('Category not found');
			$this->template->del('counts');
			return;
		}

		$this->add('xepan\cms\Controller_Tool_Optionhelper',['model'=>$category]);
		$this->setModel($category);

		$url = $category['custom_link']?$category['custom_link']:$this->options['redirect_page'];
		$url = $this->app->url($url,['xsnb_category_id'=>$this->model->id]);
		
		$description = $this->model['description'];
		$description = str_replace("{{url}}", $url, $description);
		$description = str_replace("{{category_id}}", $category->id, $description);
		$this->template->setHtml('category_description',$description);
	}

	function defaultTemplate(){
		return ['view\tool\categorydetail'];
	}
}