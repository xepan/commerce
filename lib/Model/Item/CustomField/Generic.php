<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Generic extends \xepan\base\Model_Table{
 	// public $acl = false;
 	public $actions=[
		'All'=>[
			'view',
			'edit',
			'delete'
		]
	];

 	public $table = "customfield_generic";

	function init(){
		parent::init();

		$this->addField('created_by_id')->defaultValue($this->app->employee->id);
		$this->addField('name')->sortable(true);
		$this->addField('display_type')->enum(['Line','DropDown','Color','Date','DateAndTime'])->sortable(true);
		$this->addField('sequence_order')->type('Number')->hint('show in asceding order')->sortable(true);
		$this->addField('is_filterable')->type('boolean');
		$this->addField('is_system')->type('boolean')->defaultValue(false);
		$this->addField('type')->enum(['CustomField','Specification'])->system(true);
		$this->addField('value')->type('text')->hint('comma separated multiple value');

		$this->hasMany('xepan\commerce\Item_CustomField_Association','customfield_generic_id');
		$this->hasMany('xepan\commerce\Store_TransactionRowCustomFieldValue','customfield_generic_id');
		$this->addHook('beforeSave',$this);

		//use for acl
		// $this->addExpression('type')->set("'CustomField'");
		
		$this->is([
				'name|required',
				'display_type|required',
				'type|required'
			]);
	}


	function beforeSave(){

		$c = $this->add('xepan\commerce\Model_Item_CustomField_Generic');
		$c->addCondition('name',$this['name']);
		$c->addCondition('type',$this['type']);

		if($this->loaded()){
			$c->addCondition('id','<>',$this->id);
		}

		$c->tryLoadAny();
		if($c->loaded()){
			throw $this->exception('This name is already taken');		
		}
	}
	
} 
 
	

