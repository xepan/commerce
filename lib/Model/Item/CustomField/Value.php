<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Value extends \xepan\base\Model_Table{
 	public $table = "customfield_value";

	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();


		$this->hasOne('xepan/commerce/Item','item_id');
		$this->hasOne('xepan/commerce/Item/CustomField_Association','customfield_association_id');

		$this->addField('name');

		$this->addExpression('field_name_with_value')->set(function($m,$q){
			return " 'TODO' ";
			// return $q->concat(
			// 	$q->api->db->dsql()->fx('IFNULL',array($m->add('xepan/commerce/Model_Item_Custom_Association',array('table_alias'=>'cfdept'))->addCondition('id',$q->getField('itemcustomfiledasso_id'))->fieldQuery('department_phase'),'-')),
			// 	' :: ',
			// 	$m->refSQL('customfield_id')->fieldQuery('name'),
			// 	' :: ',
			// 	$q->getField('name')
			// 	);
		});


		$this->hasMany('xepan/commerce/Item_Image','customfield_value_id');
		$this->hasMany('xepan/commerce/Item/Quantity_Condition','customfield_value_id');

		// $this->hasMany('xShop/CustomFieldValueFilterAssociation','customefieldvalue_id');

	}
} 
 
	

