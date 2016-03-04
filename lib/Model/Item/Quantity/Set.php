<?php 

 namespace xepan\commerce;

 class Model_Item_Quantity_Set extends \xepan\commerce\Model_Document{
	public $status = [];
	public $actions = [
					'*'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		$doc_j=$this->join('item.document_id');

		$doc_j->hasOne('xepan/commerce/Item','item_id');

		$doc_j->addField('name');//->sortable(true); // To give special name to a quantity Set .. leave empty to have qty value here too
		$doc_j->addField('qty')->type('number')->mandatory(true);//->sortable(true);
		$doc_j->addField('price')->type('money')->mandatory(true)->caption('Unit Price');//->sortable(true);
		$doc_j->addField('is_default')->type('boolean')->defaultValue(false);//->sortable(true);

		$this->addExpression('custom_fields_conditioned')->set(function($m,$q){
			return "'TODO'";
			$temp =$m->refSQL('xShop/QuantitySetCondition');
			return $temp->_dsql()->group('quantityset_id')->del('fields')->field('count(*)');
		});//->sortable(true);

		$doc_j->hasMany('xepan/commerce/Item/Quantity_Condition','quantityset_id');

	}
} 
 
	

