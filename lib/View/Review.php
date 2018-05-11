<?php

namespace xepan\commerce;

class View_Review extends \View{
	public $options = [
		'layout'=>'standard',
		'show_title'=>true,
		'show_date'=>true,
		'show_image'=>true,
		'sort'=>"descending",
		'show_paginator'=>true,
		'paginator_set_rows_per_page'=>"5",
		'display_review_status'=>'Approved,Pending', //Pending,Approved,Cancled comma seperated multiple values
		'review_status_for_add'=>'Pending',
		'not_login_message'=>'for leave a review please login first',
		'custom_template'=>null
	];

	public $item_model;
	
	function init(){
		parent::init();
		if($this->owner instanceof \AbstractController){
			$this->add('View')->set('I am Review tool')->addClass('alert alert-info');
			return;
		}
		
		if(!$this->item_model->loaded()){
			$this->add('View')->set('review for product/item not defined')
				->addClass('alert alert-warning');
			return;
		}

		$this->customer = $customer = $this->add('xepan\commerce\Model_Customer');
        $customer->loadLoggedIn("Customer");
        if($customer->loaded()){
			$this->addReviewForm();
        }else{
        	$this->add('View')->addClass('alert alert-warning')->set($this->options['not_login_message']);
        }

        $this->addReviewList();
	}


	function addReviewForm(){
		$add_new_review_model = $this->add('xepan\commerce\Model_Review');
		$add_new_review_model
			->addCondition('related_document_id',$this->item_model->id)
			->addCondition('related_type','xepan\commerce\Model_Item')
			;
		$form = $this->add('Form');
		// to do form layout beautify
		$form->addField('title')->validate('required');
		$form->addField('text','review')->validate('required');
		$form->addField('Dropdown','rating')->setValueList($add_new_review_model->rating_list);
		$form->addSubmit('Leave a Review');

		if($form->isSubmitted()){
			$add_new_review_model['customer_id'] = $this->customer->id;
			$add_new_review_model['name'] = $form['title'];
			$add_new_review_model['review'] = $form['review'];
			$add_new_review_model['rating'] = $form['rating'];
			$add_new_review_model['status'] = $this->options['review_status_for_add'];
			$add_new_review_model->save();

			$form->js(null,$form->js()->reload())->univ()->successMessage('Thank you for review')->execute();
		}
	}

	function addReviewList(){

		$layout = $this->options['layout'];
		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/item/detail/review/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				throw new \Exception($path);
				$this->add('View_Warning')->set('template not found');
				return;
			}else{
				$layout = $this->options['custom_template'];
			}
		}
		
		$template = 'view/tool/item/detail/review/'.$layout;

		$review_model = $this->add('xepan\commerce\Model_Review');
        $review_model->addCondition('related_document_id',$this->item_model->id);
        $review_model->addCondition('related_type',"xepan\commerce\Model_Item");
        $review_model->addCondition('status','in',explode(",", $this->options['display_review_status']));
        
		$grid = $this->add('CompleteLister',null,null,[$template]);
		$grid->setModel($review_model);
		$grid->addHook('formatRow',function($g){
			$g->current_row['human_redable_created_at'] = $this->add('xepan\base\xDate')->diff($this->app->now,$g->model['created_at']);

			$img_src = $g->model['customer_profile_image'];
			if(!$img_src)
				$img_src = "vendor/xepan/commerce/templates/images/avatar.jpg";
			$g->current_row['customer_profile_image_url'] = $img_src;
		});
	}
}


