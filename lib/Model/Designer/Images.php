<?php

 namespace xepan\commerce;

 class Model_Designer_Images extends \xepan\base\Model_Table{ 	
 	public $table="designer_images";
 	function init(){
 		parent::init();
 		
 		// $this->hasOne('xepan\base\Epan','epan_id');
 		$this->hasOne('xepan\commerce\Designer_Image_Category','designer_category_id')->display(['form'=>'xepan\commerce\Form_Field_DropDown']);
 		$this->add('xepan\filestore\Field_Image','image_id');
 		$this->addField('description')->type('text');
 		$this->addExpression('contact_id')->set($this->refSQL("designer_category_id")->fieldQuery("contact_id"));
		// $this->is([
		// 		'description|to_trim|required'
		// 		]); 		
 	}
}
 
    