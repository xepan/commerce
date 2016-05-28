<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Set extends \xepan\base\Model_Table{
 	public $acl =false;
 	public $table = "quantity_set";
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();


		$this->hasOne('xepan\commerce\Item','item_id');

		$this->addField('name');//->sortable(true); // To give special name to a quantity Set .. leave empty to have qty value here too
		$this->addField('qty')->type('number')->mandatory(true);//->sortable(true);
		$this->addField('old_price')->type('money')->mandatory(true)->caption('Unit Old Price');//->sortable(true);
		$this->addField('price')->type('money')->mandatory(true)->caption('Unit Price');//->sortable(true);
		$this->addField('is_default')->type('boolean')->defaultValue(false);
		
		$this->hasMany('xepan\commerce\Item\Quantity\Condition','quantity_set_id');

		$this->addExpression('custom_fields_conditioned')->set(function($m,$q){
			$temp =$m->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$m->id);
			return $temp->_dsql()->group('quantity_set_id')->del('fields')->field('count(*)');
		});//->sortable(true);

		$this->addExpression('conditions')->set(function($m,$q){
			$x = $m->add('xepan\commerce\Model_Item_Quantity_Condition',['table_alias'=>'qtycondition_str']);
			return $x->addCondition('quantity_set_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR "<br/>")',[$x->getElement('customfield_value')]));
		})->allowHTML(true);


		$this->addExpression('type')->set("'QuantitySet'");

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){

		$condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')->addCondition('quantity_set_id',$this->id);
		
		foreach ($condition as $value) {
			$value->delete();
		}
	}

	function deleteQtySetCondition(){
		if(!$this->loaded())
			throw new \Exception("model must loaded", 1);

		$this->add('xepan\commerce\Model_Item_Quantity_Condition')
			->addCondition('quantity_set_id',$this->id)
			->deleteAll();
			
	}

	function beforeSave(){
		if(!$this['name'])
			$this['name'] = $this['qty'];
	}

} 
 
	

