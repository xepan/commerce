<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Association extends \xepan\base\Model_Table{
 	public $acl = 'xepan\commerce\Model_Item';
	public $table = "customfield_association";
	public $status = ['Active','DeActive'];

	public $actions = [
					'active'=>['view','edit','delete','deactivate'],
					'deactivate'=>['view','edit','delete','active']
					];

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item_CustomField_Generic','customfield_generic_id')->sortable(true);//->display(array('form'=>'autocomplete/Plus'));
		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\hr\Department','department_id')->mandatory(true)->defaultValue(null)->sortable(true);
		
		$this->addField('status')->enum(['Active','DeActivate'])->defaultValue('Active')->sortable(true);
		$this->addField('group');
		$this->addField('order')->defaultValue(0)->sortable(true);
		$this->addField('can_effect_stock')->type('boolean')->defaultValue(false);
		$this->addField('is_optional')->type('boolean')->defaultValue(false);

		$this->hasMany('xepan\commerce\Item_CustomField_Value','customfield_association_id');

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('customfield_generic_id')->fieldQuery('name');
		})->sortable(true);

		$this->addExpression('display_type')->set(function($m,$q){
			return $m->refSQL('customfield_generic_id')->fieldQuery('display_type');
		});

		$this->addExpression('type')->set("'CustomFieldAssociation'");

		$this->addExpression('CustomFieldType')->set($this->refSQL('customfield_generic_id')->fieldQuery('type'));
		$this->addExpression('is_filterable')->set($this->refSQL('customfield_generic_id')->fieldQuery('is_filterable'));
		$this->addExpression('is_system')->set($this->refSQL('customfield_generic_id')->fieldQuery('is_system'));

		$this->addHook('beforeDelete',$this);
		$this->addHook('beforeSave',$this);

		$this->is([
				'customfield_generic_id|required',
				'item_id|required'
			]);
	}

	function beforeSave(){
		$old_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association');
		$old_asso->addCondition('customfield_generic_id',$this['customfield_generic_id']);
		$old_asso->addCondition('item_id',$this['item_id']);
		$old_asso->addCondition('department_id',$this['department_id']);
		$old_asso->addCondition('id','<>',$this['id']);
		$old_asso->tryLoadAny();
		if($old_asso->loaded())
			throw $this->Exception('Custom Field Already Added','ValidityCheck')->setField('customfield_generic_id');
	}

	function beforeDelete(){

		$values = $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$this->id);
		
		foreach ($values as $value) {
			$value->delete();
		}
	}

	function getCustomValue(){
		if(!$this->loaded())
			throw new \Exception("custom model must be loaded");
		$cf_value_array = array();
		/*
		values:[
					{value:9},
					{value:10},
					{
						value: 11,
						filters:{
							color: 'red' // This is filter
						}
					},
				]
		*/
		//Load Custom Field Value Model
		$cf_value_model = $this->ref('xepan\commerce\Item_CustomField_Value')->addCondition('status','Active');
			//for each of value model and get its name
			foreach ($cf_value_model as $one_cf_value_model){
				$one_value_array = array();
				// $one_value_array['value'] = $cf_value_model['name'];
				//load filter association model
				$filter_model = $this->add('xepan\commerce\Model_Filter');
				$filter_model->addCondition('customfield_association_id',$one_cf_value_model['id']);
				$count = $filter_model->tryLoadAny()->count()->getOne();
				// $one_value_array['customfield'] = $this['name'];
				// $one_value_array['customefieldvalue_id'] = $cf_value_model['id'];
				$one_value_array['filter_count'] = $count;
				//foreach filter and get filter value
				$filter_value_array = array();
				foreach ($filter_model as $filter){
					$filter_value_array[]=array($filter_model['customfield_association_id'] => $filter_model['name']);
				}
				$one_value_array['filters'] = $filter_value_array;
				$cf_value_array = array_replace($cf_value_array, array($cf_value_model['name']=>$one_value_array));
			}

		return $cf_value_array;
	}

	function duplicateValue($new_association_model,$new_item){
		if(!$this->loaded())
			throw new \Exception("model customfield association must loaded", 1);
			
		$old_values = $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$this->id);
		foreach ($old_values as $old_value) {
			$new_value = $this->add('xepan\commerce\Model_Item_CustomField_Value');
			$new_value['customfield_association_id'] = $new_association_model->id;
			$new_value['status'] = $old_value['status'];
			$new_value['name'] = $old_value['name'];
			$new_value['item_id'] = $new_item->id;
			$new_value['can_effect_stock'] = $old_value['can_effect_stock'];
			$new_value->save();

			$old_value->duplicateValueImage($new_value,$new_item);
			$old_value->duplicateQuantitySetCondition($new_value,$new_item);
			$new_value->unload();
		}
	}

} 
 
	

