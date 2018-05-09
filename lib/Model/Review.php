<?php

	namespace xepan\commerce;

	class Model_Review extends \xepan\base\Model_Table{
		public $table = "review";
		public $status = ['Active','Inactive'];
		public $actions = [
								'Active'=>['view','edit','delete','deactivate'],
								'InActive'=>['view','edit','delete','activate']
						  ];
		public $rating_list = ['1','1.5','2','2.5','3','3.5','4','4.5','5'];
		function init(){

			parent::init();
			$this->addField('customer_id')->type('int');
			$this->addField('created_at')->type('datetime');
			$this->addField('related_type');
			$this->addField('related_document_id')->type('int');
			$this->addField('review')->type('text');
			$this->addField('rating')->enum($this->rating_list);
			$this->addField('status')->enum($this->status)->defaultValue('Active');
			$this->addField('approved_by');
			$this->addField('approved_at')->type('datetime');
		
			$this->add('dynamic_model\Controller_AutoCreator');	
			$this->is([
						'customer_id|required',

					]);

		}
		function deactivate(){
			$this['status']='InActive';
			$this->save();
		}

		function activate(){
			$this['status']='Active';
			$this->save();
		}

	}
