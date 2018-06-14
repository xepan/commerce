<?php

namespace xepan\commerce;

class Model_Wishlist extends \xepan\base\Model_Table{

	public $table = "wishlist";
	public $status = ['Due','Complete','Cancel'];
	public $actions = [
					'Due'=>['view','edit','delete','complete'],
					'Complete'=>['view','edit','delete','due'],
			];
	public $acl_type = "WISHLIST";

	function init(){
		parent::init();

		$this->hasOne('xepan\base\contact','created_by_id')->system(true)->defaultValue(@$this->app->employee->id);

		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\commerce\Item','item_id');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);

		$this->addField('status')->enum($this->status)->defaultValue('Due');

		// $this->add('dynamic_model\Controller_AutoCreator');
		/*$this->is([
			'contact_id|to_trim|requried',
			'item_id|to_trim|requried',
			'created_at|to_trim|requried',
		])*/

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if(!$this['created_by_id']) $this['created_by_id'] = $this['contact_id'];
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