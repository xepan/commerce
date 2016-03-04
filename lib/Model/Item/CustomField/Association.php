<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Association extends \xepan\base\Model_Table{
	public $table = "customfield_association";
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		

		$this->hasOne('xepan/commerce/Item/CustomField_Generic','customfield_generic_id')->display(array('form'=>'autocomplete/Plus'));
		$this->hasOne('xepan/commerce/Item','item_id');
		//Todo 
		// $this->hasOne('xProduction/Phase','department_phase_id');
		
		$this->addField('can_effect_stock')->type('boolean')->defaultValue(false)->mandatory(true);
		$this->addField('status')->enum(['Active','DeActivate']);

		$this->hasMany('xepan/commerce/Item/CustomField_Value','customfield_association_id');

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('customfield_id')->fieldQuery('name');
		});

	}
} 
 
	

