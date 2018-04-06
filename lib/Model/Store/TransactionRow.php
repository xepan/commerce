<?php

namespace xepan\commerce;

class Model_Store_TransactionRow extends \xepan\base\Model_Table{

	public $table="store_transaction_row";
	public $acl=false;

	function init(){
		parent::init();

		// $this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\commerce\Model_Store_TransactionAbstract','store_transaction_id');
		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
		$this->hasOne('xepan\commerce\Item','item_id')->display(array('form'=>'xepan\commerce\Item'));
		// $this->hasOne('xepan\commerce\Item_CustomField_Association','customfield_generic_id');
		// $this->hasOne('xepan\commerce\Item_CustomField_Value','customfield_value_id');
		$this->hasOne('xepan\production\Jobcard_Detail','jobcard_detail_id');

		$this->addField('related_transaction_row_id')->type('Number');
		$this->addField('quantity')->type('Number');
		$this->addField('status')->enum(['ToReceived','Received','Shipped','Delivered','Sales_Return','Opening','Purchase','Purchase_Return','Consumption_Booked','Consumed'])->defaultValue('ToReceived'); // Shipped/Delivered used with transacion_type deliver
		$this->addField('extra_info')->type('text');
		$this->addField('serial_nos')->type('text');
		$this->addField('narration')->type('text');

		$this->hasMany('xepan\commerce\Store_TransactionRowCustomFieldValue','store_transaction_row_id',null,'StoreTransactionRowsCustomField');
		// $this->addExpression('item_id')->set($this->refSQL('qsp_detail_id')->fieldQuery('item_id'));
		$this->addExpression('type')->set($this->refSQL('store_transaction_id')->fieldQuery('type'));
		// $this->addExpression('item_name')->set($this->refSQL('item_id')->fieldQuery('name'));
		
		$this->addExpression('item_name')->set(function($m,$q){
			return $q->expr('CONCAT([0]," :: ",[1]," :: ",IFNULL([2]," "))',[$this->refSQL('item_id')->fieldQuery('name'),$this->refSQL('item_id')->fieldQuery('sku'),$this->refSQL('item_id')->fieldQuery('hsn_sac')]);
		});

		$this->addExpression('transaction_narration')->set(function($m,$q){
			return $this->add('xepan\commerce\Model_Store_TransactionAbstract')
						->addCondition('id',$m->getElement('store_transaction_id'))
						->setLimit(1)
						->fieldQuery('narration');
		})->caption('narration');

		$this->addExpression('order_item_qty_unit')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('qsp_detail_id')->fieldQuery('qty_unit')]);
		});
		
		$this->addExpression('item_qty_unit')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('qty_unit')]);
		});

		$this->addExpression('order_item_qty_unit_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('qsp_detail_id')->fieldQuery('qty_unit_id')]);
		});
		
		$this->addExpression('item_qty_unit_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('qty_unit_id')]);
		});

		$this->addExpression('created_at')->set($this->refSQL('store_transaction_id')->fieldQuery('created_at'))->sortable(true);

		$this->addExpression('related_sale_order')->set($this->refSQL('store_transaction_id')->fieldQuery('related_document_id'));
		$this->addExpression('from_warehouse_id')->set($this->refSQL('store_transaction_id')->fieldQuery('from_warehouse_id'));
		$this->addExpression('from_warehouse')->set($this->refSQL('store_transaction_id')->fieldQuery('from_warehouse'));
		$this->addExpression('to_warehouse_id')->set($this->refSQL('store_transaction_id')->fieldQuery('to_warehouse_id'));
		$this->addExpression('to_warehouse')->set($this->refSQL('store_transaction_id')->fieldQuery('to_warehouse'));
		$this->addExpression('department_id')->set($this->refSQL('store_transaction_id')->fieldQuery('department_id'));
		$this->addHook('beforeDelete',[$this,'deleteAllTransactionRowCustomFields']);
	}

	function deleteAllTransactionRowCustomFields(){
		$this->ref('StoreTransactionRowsCustomField')->each(function($o){
			$o->delete();
		});
	}

	function item(){
		return $this->ref('item_id');
	}
	

	function receive(){
		if(!$this->loaded())
			throw new \Exception("model transaction row must loaded", 1);
			
		$this['status'] = "Received";
		$this->save();
		
		return $this;
	}
}