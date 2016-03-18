<?php

 namespace xepan\commerce;

 class Model_Designer_Image_Category extends \xepan\base\Model_Table{
 	public $table="designer_image_category";
 	public $actions = ['*'=>'view','edit','delete'];
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\base\Epan','epan_id');
		
		$this->addField('name')->caption('Category Name');
		$this->addField('is_library')->type('boolean')->defaultValue(false);
		$this->hasMany('xepan\commerce\Designer_Images','designer_category_id',null,'DesignerAttachments');
	}
}
 
    