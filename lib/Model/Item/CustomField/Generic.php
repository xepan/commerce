<?php 

 namespace xepan\commerce;

 class Model_Item_CustomField_Generic extends \xepan\base\Model_Table{
 	public $acl =false;
 	public $table = "customfield_generic";

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('display_type')->enum(['Line','DropDown','Color'])->mandatory(true);
		$this->addField('sequence_order')->type('Number')->hint('show in asceding order');
		$this->addField('is_filterable')->type('boolean');
		$this->addField('type')->enum(['CustomField','Specification'])->mandatory(true)->system(true);

		$this->addHook('beforeSave',$this);

		//use for acl
		// $this->addExpression('type')->set("'CustomField'");
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
 
	

