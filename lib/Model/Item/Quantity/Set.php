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
		$this->addField('price')->type('money')->mandatory(true)->caption('Unit Price');//->sortable(true);


		$this->hasMany('xepan\commerce\Item\Quantity\Condition','quantity_set_id');

		$this->addExpression('conditions')->set(function($m,$q){
			$x = $m->add('xepan\commerce\Model_Item_Quantity_Condition',['table_alias'=>'qtycondition_str']);
			return $x->addCondition('quantity_set_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR "<br/>")',[$x->getElement('customfield_value')]));
		})->allowHTML(true);


		$this->addExpression('type')->set("'QuantitySet'");
	}
} 
 
	

