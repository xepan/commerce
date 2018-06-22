<?php

namespace xepan\commerce;

class View_Review extends \View{
	public $options = [
		'layout'=>'standard',
		'show_title'=>true,
		'show_date'=>true,
		'show_image'=>true,
		'show_review_form'=>true,
		'show_review_history'=>true,
		'sort'=>"descending",
		'paginator'=>5,
		'display_review_status'=>'Approved', //Pending,Approved,Cancled comma seperated multiple values
		'review_status_for_add'=>'Pending',
		'not_login_message'=>'for leave a review please login first',
		'custom_template'=>null,
		'rating_list'=>[1=>1,2=>2,3=>3,4=>4,5=>5],
		'rating_list_info'=>[
							1=>['progressbar_class'=>'progress-bar-danger'],
							2=>['progressbar_class'=>'progress-bar-warning'],
							3=>['progressbar_class'=>'progress-bar-info'],
							4=>['progressbar_class'=>'progress-bar-primary'],
							5=>['progressbar_class'=>'progress-bar-success'],
						],
		'show_rating_breakdown'=>true

	];

	public $related_model;
	public $related_document_type = 'xepan\commerce\Model_Item';
	public $break_down_data;
	
	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController){
			$this->add('View')->set('I am Review tool')->addClass('alert alert-info');
			return;
		}

		if($this->related_model instanceof \Model && !$this->related_model->loaded()){
			$this->add('View')->set('review for product/item not defined')
				->addClass('alert alert-warning');
			return;
		}

		$this->customer = $customer = $this->add('xepan\commerce\Model_Customer');
        $customer->loadLoggedIn("Customer");
        if($customer->loaded() && $this->options['show_review_form']){
			$this->addReviewForm();
        }else{
        	// $this->add('View')->addClass('alert alert-warning')->set($this->options['not_login_message']);
        }

		
        if($this->options['show_review_history']){
        	$this->addReviewList();
        }
		
	}


	function addReviewForm(){
		$add_new_review_model = $this->add('xepan\commerce\Model_Review');
		$add_new_review_model
			->addCondition('related_document_id',$this->related_model->id)
			->addCondition('related_type',$this->related_document_type)
			;
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
        		->showLables(true)
        		->addContentSpot()
        		->makePanelCollepsible()
        		->layout([
        				'title'=>'Leave a Review~c1~6',
        				'rating'=>'c2~6',
        				'review'=>'c3~12',
        				'FormButtons~&nbsp;'=>'c4~12'
        		]);
		// to do form layout beautify
		$form->addField('title')->validate('required');
		$form->addField('text','review')->validate('required');
		$form->addField('xepan\base\Rating','rating')
			->setValueList($this->options['rating_list']);
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
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/review/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				throw new \Exception($path);
				$this->add('View_Warning')->set('template not found');
				return;
			}else{
				$layout = $this->options['custom_template'];
			}
		}
		
		$template = 'view/tool/review/'.$layout;

		$review_model = $this->add('xepan\commerce\Model_Review');
        $review_model->addCondition('related_document_id',$this->related_model->id);
        $review_model->addCondition('related_type',$this->related_document_type);
        $review_model->addCondition('status','in',explode(",", $this->options['display_review_status']));
        $review_model->setOrder('created_at','desc');

        if($review_model->count()->getOne()){

        }
        
		$lister = $this->lister = $this->add('CompleteLister',null,null,[$template]);
		$lister->setModel($review_model);
		$lister->addHook('formatRow',function($g){
			$g->current_row['human_redable_created_at'] = $this->add('xepan\base\xDate')->diff($this->app->now,$g->model['created_at']);

			$img_src = $g->model['customer_profile_image'];
			if(!$img_src)
				$img_src = "vendor/xepan/commerce/templates/images/avatar.jpg";
			$g->current_row['customer_profile_image_url'] = $img_src;
		});

		if($this->options['paginator']){
			$paginator = $lister->add('Paginator');
			$paginator->setRowsPerPage($this->options['paginator']);
		}

		if($this->options['show_rating_breakdown']){
			$this->showRatingBreakdown();
		}else{
			$lister->template->tryDel('rating_breakdown_wrapper');
			$lister->template->trySet('avg_rating_class','col-lg-12 col-md-12 col-sm-12 col-xs-12');
		}
		
	}

	function showRatingBreakdown(){
		$r_model = $this->add('xepan\commerce\Model_Review');
		$r_model->addCondition('status','in',explode(",", $this->options['display_review_status']));
		$r_model->addCondition('related_document_id',$this->related_model->id);
        $r_model->addCondition('related_type',$this->related_document_type);

		$counts = $r_model->_dsql()->del('fields')->field('rating')->field('count(*) counts')->group('rating')->get();
		$counts_redefined =[];
		$total = 0;
		foreach ($counts as $cnt) {
			$counts_redefined[$cnt['rating']] = $cnt['counts'];
			$total += $cnt['counts'];
		}

		$this->break_down_data = $break_down_data = [];
		$total_of_rating_multiple  = 0;
		foreach ($this->options['rating_list'] as $value => $display_name) {
			$rating_count = isset($counts_redefined[$value])?$counts_redefined[$value]:0;
			$progress_class = isset($this->options['rating_list_info'][$value])?$this->options['rating_list_info'][$value]['progressbar_class']:'progress-bar-primary';
			if($total>0)
				$rating_percentage=($rating_count/$total*100);
			else
				$rating_percentage=0;

			$break_down_data[$value] = [
								'rating_level_name'=> $display_name,
								'rating_level'=> $value,
								'total'=>$total,
								'rating_count'=> $rating_count,
								'rating_percentage'=>$rating_percentage,
								'progressbar_class'=>$progress_class
			 				];

			$total_of_rating_multiple += $value * $rating_count;
		}
		rsort($break_down_data);



		$break_html = '<div class="pull-left" style="width:100%;">
		          <div class="pull-left" style="width:35px; line-height:1;">
		            <div style="height:9px; margin:5px 0;">{$rating_level_name} <span class="glyphicon glyphicon-star"></span></div>
		          </div>
		          <div class="pull-left" style="width:85%;">
		            <div class="progress" style="height:9px; margin:8px 0;">
		              <div class="progress-bar {$progressbar_class}" role="progressbar" aria-valuenow="{$rating_percentage}" aria-valuemin="0" aria-valuemax="100" style="width: {$rating_percentage}%;">
						<span class="sr-only">{$rating_percentage}% </span>		              
		              </div>
		            </div>
		          </div>
		          <div class="pull-right" style="margin-left:10px;">{$rating_count}</div>
		        </div>';

		$breakdown_wrapper = $this->lister->add('Lister',null,'rating_breakdown');
		$breakdown_wrapper->template->loadTemplateFromString($break_html);
		$breakdown_wrapper->setSource($break_down_data);
		
		if($total>0)
			$avg_rating = round(($total_of_rating_multiple/$totala),1);
		else
			$avg_rating=0;

		$this->lister->template->trySet('total_rating',end($this->options['rating_list']));
		$this->lister->template->trySet('average_rating',$avg_rating);

		$form = $this->lister->add('Form',null,'average_rating_star');
		$rating_field = $form->addField('xepan\base\Rating','rating','')
					->setValueList($this->options['rating_list']);

		$rating_field->initialRating = ($avg_rating==0)?-1:$avg_rating;
		$rating_field->readonly = true;

	}


}


