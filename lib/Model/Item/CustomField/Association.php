<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Association extends \xepan\commerce\Model_Document{
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		$doc_j=$this->join('item.document_id');

		$doc_j->hasOne('xepan/commerce/Item/CustomField_Generic','customfield_generic_id')->display(array('form'=>'autocomplete/Plus'));
		$doc_j->hasOne('xepan/commerce/Item','item_id');

		//Todo 
		// $this->hasOne('xProduction/Phase','department_phase_id');

		$doc_j->addField('can_effect_stock')->type('boolean')->defaultValue(false)->mandatory(true);

		$doc_j->hasMany('xepan/commerce/Item/CustomField_Value','customfield_association_id');

		$doc_j->addExpression('name')->set(function($m,$q){
			return $m->refSQL('customfield_id')->fieldQuery('name');
		});

	}
} 
 
	

