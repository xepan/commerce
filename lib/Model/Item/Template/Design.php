<?php

namespace xepan\commerce;

class Model_Item_Template_Design extends \xepan\base\Model_Table{
	public $table = "item_template_design";
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\commerce\SalesOrder','order_id');
			
		$this->addField('name');
		
		$this->addField('last_modified')->type('date')->defaultValue(date('Y-m-d'));
		// $this->addField('is_ordered')->type('boolean')->defaultValue(false);
		$this->addField('designs')->type('text');

		$this->addExpression('item_name')->set(function($m,$q){
			// return "'todo'";
			return $q->expr("IFNULL([0],[1])",[$m->getElement('name'),$m->refSQL('item_id')->fieldQuery('name')]);
		});

		$this->addExpression('item_sku')->set(function($m,$q){
			// return "'todo'";
			return $q->expr("[0]",[$m->refSQL('item_id')->fieldQuery('sku')]);
		});

		$this->addExpression('order_document_no')->set(function($m,$q){
			return $q->expr("[0]",[$m->refSQL('order_id')->fieldQuery('document_no')]);
		});

	}

	function afterSave(){
		$item = $this->ref('item_id');
		$item['is_publish']= false;
		$item['is_party_publish']= false;
		$item->save();
	}

	function duplicate($customer_id, $item_id, $item_design){
		$model_design = $this->add('xepan\commerce\Model_Item_Template_Design');
		
		$model_design['contact_id'] = $customer_id;
		$model_design['item_id'] = $item_id;
		$model_design['designs'] = $item_design;
		$model_design->save();
	}
}