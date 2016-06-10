<?php
namespace xepan\commerce;
class View_CategoryLister extends \CompleteLister{
		public $options = [
		'url_page' =>'indeqqqx'
	];

	function init(){
		parent::init();

		$model = $this->setModel('xepan\commerce\Category')
				->addCondition('parent_category_id',0)
				->addCondition('status','Active');
		
	}
	
	function formatRow(){
		$url = $this->model['custom_link']?$this->model['custom_link']:$this->options['url_page'];

		$sub_cat = $this->add('xepan\commerce\Model_Category',['name'=>'model_child_'.$this->model->id]);
		$sub_cat->addCondition('parent_category_id',$this->model->id);

		if($sub_cat->count()->getOne() > 0){								
			$sub_c =$this->add('xepan\commerce\View_CategoryLister',null,'nested_category',['view\tool\categorylister','category_list']);
			$sub_c->setModel($sub_cat);
			$this->current_row_html['nested_category']= $sub_c->getHTML();
		}else
			$this->current_row_html['nested_category']= "";
		
		$this->current_row_html['url']= $this->app->url($url,['xsnb_category_id'=>$this->model->id]);

		parent::formatRow();
	}

	function defaultTemplate(){
		return ['view\tool\categorylister'];
	}
}