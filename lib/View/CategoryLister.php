<?php
namespace xepan\commerce;
class View_CategoryLister extends \CompleteLister{
		public $options = [
		'url_page' =>'index',
		'custom_template'=>"categorylister"
	];

	function init(){
		parent::init();

		$model = $this->add('xepan\commerce\Model_Category');
		$model->addCondition($model->dsql()->orExpr()->where('parent_category_id',0)->where('parent_category_id',null))
				->addCondition('status','Active');
		$this->setModel($model);
	}
	
	function formatRow(){

		//calculating url
		if($this->model['custom_link']){
			// if custom link contains http or https then redirect to that website
			$has_https = strpos($this->model['custom_link'], "https");
			$has_http = strpos($this->model['custom_link'], "http");
			if($has_http === false or $has_https === false )
				$url = $this->app->url($this->model['custom_link'],['xsnb_category_id'=>$this->model->id]);
			else
				$url = $this->model['custom_link'];
			$this->current_row_html['url'] = $url;
		}else{
			$url = $this->app->url($this->options['url_page'],['xsnb_category_id'=>$this->model->id]);
			$this->current_row_html['url'] = $url;
		}

		$sub_cat = $this->add('xepan\commerce\Model_Category',['name'=>'model_child_'.$this->model->id]);
		$sub_cat->addCondition('parent_category_id',$this->model->id);

		if($sub_cat->count()->getOne() > 0){
			$sub_c =$this->add('xepan\commerce\View_CategoryLister',['options'=>$this->options],'nested_category',['view\tool\categorylister','category_list']);
			$sub_c->setModel($sub_cat);
			$this->current_row_html['nested_category']= $sub_c->getHTML();
		}else
			$this->current_row_html['nested_category']= "";
		

		parent::formatRow();
	}

	function defaultTemplate(){
		return ['view\tool\\'.$this->options['custom_template']];
	}
}