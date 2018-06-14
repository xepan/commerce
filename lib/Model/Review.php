<?php

	namespace xepan\commerce;

	class Model_Review extends \xepan\base\Model_Table{
		public $table = "review";
		public $status = ['Pending','Approved','Canceled'];
		public $actions = [
							'Pending'=>['view','approved','cancel','edit','delete'],
							'Approved'=>['view','cancel','edit','delete'],
							'Canceled'=>['view','pending','edit','delete']
						];
		// public $rating_list = [1=>1,2=>2,3=>3,4=>4,5=>5];
		public $acl_type = "ReviewAndRating";

		function init(){
			parent::init();
			
			$this->hasOne('xepan\base\contact','created_by_id')->system(true)->defaultValue(@$this->app->employee->id);
			$this->hasOne('xepan\base\Contact','customer_id');
			$this->hasOne('xepan\hr\Employee','approved_by_id');

			$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
			$this->addField('related_type');
			$this->addField('related_document_id')->type('int');
			$this->addField('name')->caption('Title');
			$this->addField('review')->type('text');
			$this->addField('rating');//->enum($this->rating_list);
			$this->addField('status')->enum($this->status)->defaultValue('Pending');
			$this->addField('approved_at')->type('datetime');
			
			$this->addExpression('customer_profile_image')
					->set($this->refSQL('customer_id')->fieldQuery('image'));

			// $this->add('dynamic_model\Controller_AutoCreator');
			$this->is([
					'customer_id|required',
					'review|to_trim|required',
					'rating|to_trim|required'
				]);
		}


		function approved(){
			$this['status'] = 'Approved';
			$this['approved_by_id'] = $this->app->employee->id;
			$this['approved_at'] = $this->app->now;
			$this->save();
		}

		function cancel(){
			$this['status'] = 'Canceled';
			$this->save();
		}

		function pending(){
			$this['status']='Pending';
			$this->save();
		}

	}
