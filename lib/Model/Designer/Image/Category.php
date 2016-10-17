<?php

 namespace xepan\commerce;

 class Model_Designer_Image_Category extends \xepan\base\Model_Table{
 	public $table="designer_image_category";
 	public $actions = ['*'=>'view','edit','delete'];
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Contact','contact_id');
		// $this->hasOne('xepan\base\Epan','epan_id');
		
		$this->addField('name')->caption('Category Name');
		$this->addField('is_library')->type('boolean')->defaultValue(false);
		$this->hasMany('xepan\commerce\Designer_Images','designer_category_id',null,'DesignerAttachments');
			
		// $this->addExpression('image_count')->set(function($m,$q){
		// 	return $m->refSQL('DesignerAttachments')->count();			
		// });
		$this->addHook('beforeDelete',[$this,'checkImageAssociation']);
		
		$this->is([
				'name|to_trim|required'
				]);
		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){	
		$c = $this->add('xepan\commerce\Model_Designer_Image_Category');
		$c->addCondition('name',$this['name']);
		$c->addCondition('contact_id',$this['contact_id']);

		if($this->loaded()){
			$c->addCondition('id','<>',$this->id);
		}

		$c->tryLoadAny();
		if($c->loaded()){
			throw $this->exception('this name is already taken', 'ValidityCheck')->setField('name');
		}
	}

	function checkImageAssociation(){
		if($count = $this->ref('DesignerAttachments')->count()->getOne()){			
			return $this->app->js()->univ()->errorMessage('Delete associated images first')->execute();
		}
	}

	function loadCategory($category_name){

		$contact = $this->add('xepan/base/Model_Contact');
  		$contact->loadLoggedIn();

  		
		$this->addCondition('name',$category_name);
		$this->addCondition('contact_id',$contact->id);
		$this->addCondition('is_library',false);
		$this->tryLoadAny();

		if(!$this->loaded()){
			$this->save();
		}
		return $this;
	}


}
 
    