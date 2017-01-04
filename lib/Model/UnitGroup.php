<?php

 namespace xepan\commerce;

 class Model_UnitGroup extends \xepan\base\Model_Table{
 	public $table="unit_group";
 	public $actions = ['*'=>['view','edit','delete']];
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('xepan\commerce\Unit','unit_group_id');

		$this->is([
				'name|to_trim|required'
			]);
	}
}
 
    