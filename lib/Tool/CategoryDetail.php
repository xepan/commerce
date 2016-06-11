<?php

namespace xepan\commerce;

class Tool_CategoryDetail extends \xepan\cms\View_Tool{
	public $options = [
				'show_image'=>true,
				'show_price'=>true,
				'show_description'=>true,
				'show_item_count' =>true,
				'include_child_category'=>true,
				'redirect_page'=>'index',
				'custom_template'=>''
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

		// if(!$this->options['show_item_count']){			
		// 	$this->template->del('item_count_wrapper');
		// }
		// if(!$this->options['show_image']){			
		// 	$this->template->del('image_wrapper');
		// }
		// if(!$this->options['show_category_description']){			
		// 	$this->template->del('description_wrapper');
		// }
		// if(!$this->options['show_price']){			
		// 	$this->template->del('item_count_wrapper');
		// }
		// if(!$this->options['show_item_count']){			
		// 	$this->template->del('item_count_wrapper');
		// }

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