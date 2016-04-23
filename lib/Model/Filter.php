<?php 

 namespace xepan\commerce;

 class Model_Filter extends \xepan\base\Model_Table{
 	public $acl =false;
	public $table = "filter";
	public $status = ['Active','DeActive'];
	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Category','category_id')->mandatory(true);
		$this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_association_id')->mandatory(true);
		
		$this->addExpression('type')->set(
				$this->refSQL('customfield_association_id')
					->fieldQuery('CustomFieldType')
			);
		
		$this->addExpression('name')->set(
				$this->refSQL('customfield_association_id')
					->fieldQuery('name')
			);

		$this->addExpression('specification_id')->set(
				$this->refSQL('customfield_association_id')
					->fieldQuery('customfield_generic_id')
			);
		$this->addExpression('item_id')->set($this->refSQL('customfield_association_id')->fieldQuery('item_id'));
		$this->addCondition('type','Specification');

		$this->addField('unique_value')->type('text')->mandatory(true)->hint('comma separated multiple value');
	}
} 
 
	

