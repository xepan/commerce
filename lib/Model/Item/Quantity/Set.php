<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Set extends \xepan\base\Model_Table{
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

		$this->addExpression('custom_fields_conditioned')->set(function($m,$q){
			return "'TODO'";
			$temp =$m->refSQL('xShop/QuantitySetCondition');
			return $temp->_dsql()->group('quantityset_id')->del('fields')->field('count(*)');
		});//->sortable(true);

		$this->hasMany('xepan\commerce\Item_Quantity_Condition','quantity_set_id');

	}
} 
 
	

