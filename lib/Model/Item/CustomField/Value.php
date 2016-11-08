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
		$this->addField('highlight_it')->type('boolean')->defaultValue(false);
		
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

		$this->addExpression('item_id')->set($this->refSQL('customfield_association_id')->fieldQuery('item_id'));

		$this->hasMany('xepan\commerce\Item_Image','customfield_value_id');
		$this->hasMany('xepan\commerce\Item_Quantity_Condition','customfield_value_id');
		$this->hasMany('xepan\commerce\Store_TransactionRowCustomFieldValue','customfield_value_id');

		// $this->hasMany('xShop/CustomFieldValueFilterAssociation','customefieldvalue_id');
		$this->addExpression('type')->set("'CustomFieldValue'");

		$this->addHook('beforeDelete',$this);
		$this->addHook('beforeSave',$this);

		$this->is([
				'name|required',
				'status|required',
			]);

	}

	function beforeSave(){
		$value = $this->add('xepan\commerce\Model_Item_CustomField_Value')
					->addCondition('customfield_association_id',$this['customfield_association_id'])
					->addCondition('name',$this['name'])
					->addCondition('id','<>',$this['id'])
					->tryLoadAny()
				;
		if($value->loaded()){
			throw $this->exception('value already added', 'ValidityCheck')->setField('name');
		}
	}

	function beforeDelete(){
		if(!$this->loaded())
			throw new \Exception("model value must loaded", 1);

		$images = $this->add('xepan\commerce\Model_Item_Image')
						->addCondition('customfield_value_id',$this->id)
						->tryLoadAny()->deleteAll();
						
		$condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('customfield_value_id',$this->id)->tryLoadAny()->deleteAll();
	}

	function duplicateValueImage($new_value,$new_item){
		if(!$this->loaded())
			throw new \Exception("model cf association values must loaded", 1);
		
		$old_images = $this->add('xepan\commerce\Model_Item_Image')->addCondition('customfield_value_id',$this->id);
		
		foreach ($old_images as $old_image) {
			$new_image = $this->add('xepan\commerce\Model_Item_Image');
			$new_image['customfield_value_id'] = $new_value->id;
			$new_image['file_id'] = $old_image['file_id'];
			$new_image['item_id'] = $new_item->id;
			$new_image->saveAndUnload();
		}
	}

	function duplicateQuantitySetCondition($new_value,$new_image){
		if(!$this->loaded())
			throw new \Exception("model cf association must loaded", 1);
			
		$old_qty_conditions = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('customfield_value_id',$this->id);
		foreach ($old_qty_conditions as $condition) {
			$new_condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition');
			$new_condition['customfield_value_id'] = $new_value->id;
			$new_condition['quantity_set_id'] = $condition['quantity_set_id'];
			$new_condition['type'] = $condition['type'];
			$new_condition->saveAndUnload();
		}
	}

}