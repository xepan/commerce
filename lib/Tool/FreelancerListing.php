<?php

namespace xepan\commerce;

class Tool_FreelancerListing extends \xepan\cms\View_Tool{
	public $options = [
						'show_design_count'=>true,
						'show_sale_count'=>true,
						'show_contact'=>true,
						'show_email'=>true,
						'freelance_result_page'=>''
					];

	function init(){
		parent::init();
		$cat_id = $this->app->stickyGET('freelancercategory_id');
		$cat_slug_url = $this->app->stickyGET('freelancercategory_slug_url');
		
		if($this->options['freelance_result_page']==''){
			$this->add('View_Error')->set('Please Specify Designer Design page Url First');
			return ;
		}

		
		$free_cat = [];
		$cat_m = $this->add('xepan\commerce\Model_FreelancerCategory')->addCondition('status','Active');
		foreach ($cat_m as $m) {
			$free_cat[$m['slug_url']] = $m->id;
		}

		$customer = $this->add('xepan\commerce\Model_Customer');
		$cus_j = $customer->join('freelancer_cat_customer_asso.customer_id');
		$cus_j->addField('freelancer_category_id');

		$customer->addCondition('status','Active');

		if($this->app->enable_sef && $cat_slug_url){
			$cat_id = $free_cat[$cat_slug_url];
		}

		if($cat_id){
			$customer->addCondition('freelancer_category_id',$cat_id);
		}

		$customer->addExpression('total_sale_design')->set(function($m,$q){
			return $m->add('xepan\commerce\Model_QSP_Detail')
				->addCondition('item_designer_id',$q->getField('id'))
				->sum('quantity');
			
		});
		// $contact->dsql()->group('designer_id');

		$c =  $this->add('CompleteLister',null,null,['view\tool\freelancerlisting']);
		$c->setModel($customer);
		$c->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$customer]);

		$c->addHook('formatRow',function($l){
			if($l->model['image'])
				$l->current_row_html['profile_image']=$l->model['image'];
			else
				$l->current_row_html['profile_image']="vendor/xepan/commerce/templates/images/avatar.jpg";

		});
	}

	function addToolCondition_row_show_design_count($value, $l){
		if(!$value){
			$l->current_row_html['design_wrapper'] = "";
			return;
		}
		$item = $this->add('xepan\commerce\Model_Item');
		$item->addCondition('designer_id',$l->model->id);
		$l->current_row['design_count'] = $item->count()->getOne();
	}

	function addToolCondition_row_show_sale_count($value, $l){
		if(!$value){
			$l->current_row_html['sales_wrapper'] = "";
			return;
		}

		$l->current_row['sales_design'] = $l->model['total_sale_design'];

	}


	function addToolCondition_row_show_contact($value, $l){
		if(!$value){
			$l->current_row_html['contact_wrapper'] = "";
			return;
		}
	}
	function addToolCondition_row_show_email($value, $l){
		if(!$value){
			$l->current_row_html['email_wrapper'] = "";
			return;
		}
	}

	function addToolCondition_row_freelance_result_page($value,$l){
		$design_page_url = $this->api->url($this->options['freelance_result_page']);
		$design_page_url->arguments = ['designer_id'=>$l->model->id];
		$l->current_row_html['page_url'] = $design_page_url;
	}
}