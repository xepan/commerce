<?php

 namespace xepan\commerce;

 class Model_Category extends \xepan\hr\Model_Document{
 	public $status = ['Active','DeActive'];
 	public $actions = [
 						'Active'=>['view','edit','delete','deactivate'],
 						'InActive'=>['view','edit','delete','activate']
 					];
	var $table_alias = 'category';

	function init(){
		parent::init();

		$cat_j=$this->join('category.document_id');

		$cat_j->hasOne('xepan\commerce\ParentCategory','parent_category_id')->defaultValue('Null');

		$cat_j->addField('name');
		$cat_j->addField('display_sequence')->type('int')->hint('change the sequence of category, sort by decenting order');
		$cat_j->addField('alt_text')->hint('set alt_text of image tag');
		$cat_j->addField('description')->type('text');//->display(array('form'=>'RichText'));

		$cat_j->addField('custom_link');
		$cat_j->addField('meta_title');
		$cat_j->addField('meta_description');
		$cat_j->addField('meta_keywords');

		$this->add('filestore\Field_Image','cat_image_id')->display(['form'=>'xepan\base\Upload'])->from($cat_j);
		
		$cat_j->hasMany('xepan\commerce\Filter','category_id');
		$cat_j->hasMany('xepan\commerce\CategoryItemAssociation','category_id');
		$cat_j->hasMany('xepan\commerce/Category','parent_category_id',null,'SubCategories');

		$this->addCondition('type','Category');
		$this->getElement('status')->defaultValue('Active');

		$this->addExpression('item_count')->set(function($m){
			return $m->refSQL('xepan\commerce\CategoryItemAssociation')
							->addCondition('is_template', false)->count();
		});

		$this->addExpression('template_count')->set(function($m){
			return $m->refSQL('xepan\commerce\CategoryItemAssociation')
						  ->addCondition('is_template', true)->count();	
		});

		$this->addHook('beforeDelete',$this);	}

	function activate(){
		$this['status'] = "Active";
		$this->save();
	}

	function deactivate(){
		$this['status'] = "InActive";
		$this->save();
	}

	function beforeDelete($m){
		$this->ref('xepan\commerce\CategoryItemAssociation')->deleteAll();
	}
}