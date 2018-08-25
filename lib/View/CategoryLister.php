<?php
namespace xepan\commerce;
class View_CategoryLister extends \CompleteLister{
		public $options = [
			'url_page' =>'index',
			"custom_template"=>'',
			'show_name'=>true,
			'show_price'=>false,
			'show_image'=>false,
			'show_item_count'=>false,
			'include_sub_category'=>true,
			'show_only_parent'=>false,
			'show_only_sub_category'=>false,
			'show_only_sub_category_of_ids'=>0 // comma seperated multiple values
		];

	function init(){
		parent::init();
		// throw new \Exception($this->options['custom_template'], 1);
		$cat_ids = [];
		if($_GET['xsnb_category_id'])
			$cat_id[$_GET['xsnb_category_id']] = $_GET['xsnb_category_id'];

		if($this->options['show_only_sub_category_of_ids']){
			$cat_ids = explode(",", $this->options['show_only_sub_category_of_ids']);
			$cat_ids = array_combine($cat_ids, $cat_ids);
		}
				
		$model = $this->add('xepan\commerce\Model_Category');

		if($this->options['show_only_sub_category'] AND count($cat_ids) ){
			$model->addCondition('parent_category_id',$cat_ids);
		}else{
			$model->addCondition($model->dsql()->orExpr()->where('parent_category_id',0)->where('parent_category_id',null));
		}
		$model->addCondition('status','Active')
				->addCondition('is_website_display',true)
				;
		$model->setOrder('display_sequence','desc');
		$this->setModel($model);

		$this->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$model]);
	}
	
	function formatRow(){
		if($this->model['id'] == $_GET['xsnb_category_id']){
			$this->current_row_html['active_category'] = "active";
		}else{
			$this->current_row_html['active_category'] = "";
		}
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

		}elseif($this->app->enable_sef){
			$url = $this->app->url($this->options['url_page'].'/'.$this->model['sef_url']);
			$url->arguments = [];
			$this->current_row_html['url'] = $url;
		}else{
			$url = $this->app->url($this->options['url_page'],['xsnb_category_id'=>$this->model->id]);
			$this->current_row_html['url'] = $url;
		}

		if($this->options['include_sub_category']){
			$sub_cat = $this->add('xepan\commerce\Model_Category',['name'=>'model_child_'.$this->model->id]);
			$sub_cat->addCondition('parent_category_id',$this->model->id);
			$sub_cat->addCondition('status',"Active");
			$sub_cat->addCondition('is_website_display',true);
			$sub_cat->setOrder('display_sequence','desc');

			if($sub_cat->count()->getOne() > 0){
				$sub_c =$this->add('xepan\commerce\View_CategoryLister',['options'=>$this->options],'nested_category',['view\tool\/'.$this->options['custom_template'],'category_list']);
				$sub_c->setModel($sub_cat);
				$this->current_row_html['nested_category']= $sub_c->getHTML();
			}else{
				$this->current_row_html['nested_category'] = "";
				// $this->current_row_html['nested_category_wrapper'] = "";
			}
		}
		

		parent::formatRow();
	}

	function defaultTemplate(){
		return ['view/tool/'.$this->options['custom_template']];
	}

	function addToolCondition_row_show_item_count($value,$l){
		if(!$value)
			$l->current_row_html['item_count_wrapper'] = "";
		else
			$l->current_row_html['item_count'] = $l->model['website_display_item_count'];
	}

	function addToolCondition_row_show_image($value,$l){		
		if(!$value)
			$l->current_row_html['image_wrapper'] = "";
		else
			$l->current_row_html['category_image_url'] = $l->model['cat_image'];
	}


	function addToolCondition_row_show_price($value,$l){
		if(!$value)
			$l->current_row_html['price_wrapper'] = "";
		else{
			$l->current_row_html['min_price'] = $l->model['min_price'];	
			$l->current_row_html['max_price'] = $l->model['max_price'];
		}
	}

}