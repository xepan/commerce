<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Value extends \xepan\commerce\Model_Document{
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		$doc_j=$this->join('item.document_id');

		$doc_j->hasOne('xepan/commerce/Item','item_id');
		$doc_j->hasOne('xepan/commerce/Item/CustomField_Association','itemcustomfieldassociation_id');

		$doc_j->addField('name');

		$doc_j->addExpression('field_name_with_value')->set(function($m,$q){
			return " 'TODO' ";
			// return $q->concat(
			// 	$q->api->db->dsql()->fx('IFNULL',array($m->add('xepan/commerce/Model_Item_Custom_Association',array('table_alias'=>'cfdept'))->addCondition('id',$q->getField('itemcustomfiledasso_id'))->fieldQuery('department_phase'),'-')),
			// 	' :: ',
			// 	$m->refSQL('customfield_id')->fieldQuery('name'),
			// 	' :: ',
			// 	$q->getField('name')
			// 	);
		});


		$doc_j->hasMany('xepan/commerce/Item_Image','customfieldvalue_id');
		$doc_j->hasMany('xepan/commerce/Item/Quantity_Condition','customfieldvalue_id');

		// $doc_j->hasMany('xShop/CustomFieldValueFilterAssociation','customefieldvalue_id');

	}
} 
 
	

