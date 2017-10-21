<?php

namespace xepan\commerce;

class Tool_FreelancerCategory extends \xepan\cms\View_Tool{
	public $options = [
						'show_member_count'=>true,
						'freelance_category_result_page'=>''
					];

	function init(){
		parent::init();

		$category = $this->add('xepan\commerce\Model_FreelancerCategory');
		$category->addCondition('status','Active');
		// $contact->dsql()->group('designer_id');
		$c =  $this->add('CompleteLister',null,null,['view\tool\freelancercategory']);
		$c->setModel($category);
		$c->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$category]);

	}

	function addToolCondition_row_show_member_count($value, $l){
		if(!$value){
			$l->current_row_html['member_count_wrapper'] = "";
			return;
		}

		$cat_asso = $this->add('xepan\commerce\Model_FreelancerCatAndCustomerAssociation');
		$cat_asso->addCondition('freelancer_category_id',$l->model->id);

		$l->current_row['member_count'] = $cat_asso->count()->getOne();
	}
	function addToolCondition_row_freelance_category_result_page($value,$l){
		if($_GET['xsnb_category_id'])
			$this->app->stickyForget('xsnb_category_id');

		if($this->app->enable_sef){
			$url = $this->api->url($this->options['freelance_category_result_page']."/".$l->model['slug_url']);
			$url->arguments = [];
			$design_page_url = $url;
		}elseif($value)
			$design_page_url = $this->api->url($this->options['freelance_category_result_page'],['freelancercategory_id'=>$l->model->id]);
		else
			$design_page_url = $this->api->url(null,['freelancercategory_id'=>$l->model->id]);

		$l->current_row_html['url'] = $design_page_url;
	}

}