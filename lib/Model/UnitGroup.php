<?php

 namespace xepan\commerce;

 class Model_UnitGroup extends \xepan\base\Model_Table{
 	public $table="unit_group";
 	public $actions = ['*'=>['view','edit','delete']];
 	public $acl_type = "UnitGroup";
	function init(){
		parent::init();
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultvalue($this->app->employee->id);
		$this->addField('name');
		$this->hasMany('xepan\commerce\Unit','unit_group_id');

		$this->is([
				'name|to_trim|required'
			]);
	}
}
 
    