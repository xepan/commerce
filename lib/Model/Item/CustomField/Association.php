<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Association extends \xepan\base\Model_Table{
 	public $acl ="parent";
	public $table = "customfield_association";
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item_CustomField_Generic','customfield_generic_id');//->display(array('form'=>'autocomplete/Plus'));
		$this->hasOne('xepan\commerce\Item','item_id');
		//Todo 
		// $this->hasOne('xProduction/Phase','department_phase_id');
		
		$this->addField('can_effect_stock')->type('boolean')->defaultValue(false);
		$this->addField('status')->enum(['Active','DeActivate'])->defaultValue('Active');

		$this->hasMany('xepan\commerce\Item_CustomField_Value','customfield_association_id');

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('customfield_generic_id')->fieldQuery('name');
		});

	}
} 
 
	

