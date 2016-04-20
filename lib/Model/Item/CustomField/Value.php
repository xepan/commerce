<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Value extends \xepan\base\Model_Table{
 	public $acl =false;
 	public $table = "customfield_value";
 	public $title_field ='field_name_with_value';

	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();


		$this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_association_id');

		$this->addField('name');
		$this->addField('status')->enum(['Active','DeActive'])->defaultValue('Active');

		$this->addExpression('field_name_with_value')->set(function($m,$q){
			
			return $q->expr('CONCAT_WS(" :: ",[0],[1])',
						[
							$m->refSQL('customfield_association_id')->fieldQuery('customfield_generic'),
							$m->getElement('name')
						]);
		});

		$this->addExpression('customfield_name')->set(function($m,$q){
			return $m->refSQL('customfield_association_id')->fieldQuery('name');
		});
		
		$this->addExpression('customfield_type')->set(function($m,$q){
			return $m->refSQL('customfield_association_id')->fieldQuery('CustomFieldType');
		});

		$this->hasMany('xepan\commerce\Item_Image','customfield_value_id');
		$this->hasMany('xepan\commerce\Item_Quantity_Condition','customfield_value_id');

		// $this->hasMany('xShop/CustomFieldValueFilterAssociation','customefieldvalue_id');
		$this->addExpression('type')->set("'CustomFieldValue'");

		$this->addHook('beforeDelete',$this);
	}


	function beforeDelete(){
		if(!$this->loaded())
			throw new \Exception("model value must loaded", 1);
			
		$images = $this->add('xepan\commerce\Model_Item_Image')
						->addCondition('customfield_value_id',$this->id)
						->tryLoadAny()->deleteAll();

		$condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('customfield_value_id',$this->id)->tryLoadAny()->deleteAll();

	}
} 
 
	

