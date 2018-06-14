<?php
	namespace xepan\commerce;

	class Tool_Review extends \xepan\cms\View_Tool{
		public $options = [

					'show_title'=>true,
					'show_date'=>true,
					'show_image'=>true,
					'sort'=>"descending",
					'show_paginator'=>true,
					'paginator_set_rows_per_page'=>"5"
				];
		public $review_model;
		function init(){
			parent::init();
			if($this->owner instanceof \AbstractController){
				$this->add('View')->set('I am Review tool')->addClass('alert alert-info');
				return;
			}
			
			$this->review_model = $review_model = $this->add('xepan\commerce\Model_Review');
			$review_model->addCondition('status','Active');			
			$crud = $this->add('CRUD');
			$crud->setModel($review_model);
		
		}
	}


