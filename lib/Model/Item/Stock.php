<?php

namespace xepan\commerce;

class Model_Item_Stock extends \xepan\commerce\Model_Item{
	
	/**
	item_custom_field=['custom_field1'=>'value','custome_field_2'=>value]
	**/
	public $item_custom_field = ['Paper Type'=>'Alpha-Absolute White'];

	function init(){
		parent::init();

		$this->getElement('total_orders')->destroy();
		$this->getElement('total_sales')->destroy();

		$this->addExpression('booked')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow')
				->addCondition('item_id',$m->getElement('id'))
				->addCondition('type','Consumption_Booked');
				
				foreach ($this->item_custom_field as $cf_name => $cf_value) {
					$model->addCondition('extra_info','like','%'.$cf_name.'<=>%~'.$cf_value.'||%');
				}
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});
		
	}
}