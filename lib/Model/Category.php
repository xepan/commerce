<?php

 namespace xepan\commerce;

 class Model_Category extends \xepan\hr\Model_Document{
 	public $status = ['Active','DeActive'];
 	public $actions = [
 						'Active'=>['view','edit','delete','deactivate'],
 						'DeActive'=>['view','edit','delete','activate']
 					];
	var $table_alias = 'category';

	function init(){
		parent::init();

		$cat_j=$this->join('category.document_id');

		$cat_j->hasOne('xepan\commerce\ParentCategory','parent_category_id')->defaultValue('Null');

		$cat_j->addField('name');
		$cat_j->addField('display_sequence')->type('int')->hint('chnage the sequence of category, sort by decenting order');
		$cat_j->addField('alt_text')->hint('set alt_text of image tag');
		$cat_j->addField('description')->type('text');//->display(array('form'=>'RichText'));

		$cat_j->addField('meta_title');
		$cat_j->addField('meta_description');
		$cat_j->addField('meta_keywords');

		// $cat_j->add('filestore/Field_Image','cat_image_id');
		// $parent_join = $cat_j->leftJoin('xepan\commerce/category','parent_document_id');

		// $this->addExpression('category_name')->set(" 'Category Name: Parent Category Name' ");
		
		// $this->hasMany('xepan\commerce/Category','parent_document_id',null,'SubCategories');
		$cat_j->hasMany('xepan\commerce\Filter','category_id');
		$cat_j->hasMany('xepan\commerce\CategoryItemAssociation','category_id');

		$this->addCondition('type','Category');
		$this->getElement('status')->defaultValue('Active');
	}

	function activate(){
		$this['status'] = "Active";
		$this->saveAndUnload();
	}

	function deactivate(){
		$this['status'] = "DeActive";
		$this->saveAndUnload();
	}
}
 
    