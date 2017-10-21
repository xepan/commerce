<?php

namespace xepan\commerce;


/**
* 
*/
class Model_FreelancerCategory extends \xepan\base\Model_Table{
	public $table = "freelancer_category" ;
	public $acl = false;
	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('status')->enum(['Active','Inactive']);
		$this->addField('slug_url')->system(true);

		$this->hasMany('xepan/commerce/FreelancerCatAndCustomerAssociation','freelancer_category_id');
		
		$this->is([
				'name|to_trim|required'
			]);

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$this['slug_url'] = $this->app->normalizeSlugUrl($this['name']);

		$old = $this->add('xepan\commerce\Model_FreelancerCategory');
		$old->addCondition('slug_url',$this['slug_url']);
		$old->addCondition('id','<>',$this->id);
		$old->tryLoadAny();
		if($old->loaded())
			throw $this->Exception('slug Already Exist','ValidityCheck')->setField('slug_url');
		
	}
}