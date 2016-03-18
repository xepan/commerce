<?php

 namespace xepan\commerce;

 class Model_Designer_Image_Category extends \xepan\hr\Model_Document{
 	public $actions = ['*'=>'view','edit','delete'];

	function init(){
		parent::init();

		$document_j = $this->join('designer_image_category.document_id');
		$document_j->hasOne('xepan\base\Contact','contact_id');
		
		$document_j->addField('name')->caption('Category Name');
		$document_j->addField('is_library')->type('boolean')->defaultValue(false);

	}
}
 
    