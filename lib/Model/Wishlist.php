<?php

namespace xepan\commerce;

class Model_Wishlist extends \xepan\base\Model_Table{
	public $table = "wishlist";
	public $status = ['Due','Complete','Cancel'];
	public $actions = [
				'Due'=>['view','edit','delete','complete'],
				'Complete'=>['view','edit','delete','due'],
				'Cancel'=>['view','edit','delete','due'],
			];

	function init(){
		parent::init();

			$this->hasOne('xepan\base\Contact','contact_id');
			$this->hasOne('xepan\commerce\Item','item_id');
			$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);

			$this->addField('status')->enum($this->status)->defaultValue('Due');

			// $this->add('dynamic_model\Controller_AutoCreator');
	}

	function complete(){
		$this['status'] = "Complete";
		$this->save();
	}

	function cancel(){
		$this['status'] = "Cancel";
		$this->save();	
	}

	function due(){
		$this['status'] = "Due";
		$this->save();
	}

}