<?php

namespace xepan\commerce;

class Model_Store_TransactionAbstract extends \xepan\base\Model_Table{
	public $table="store_transaction";
	
	function init(){
		parent::init();

		// $this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','from_warehouse_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','to_warehouse_id');
		$this->hasOne('xepan\production\Jobcard','jobcard_id');
		$this->addField('type'); //Store_DispatchRequest, Store_Delivered, Store_Transaction
		
		$this->addField('related_document_id')->sortable(true); //Sale Ordre/Purchase
		// $this->addField('document_type'); //Purchase/Sale/Dispatch/Deliver
		$this->addField('created_at')->defaultValue(date('Y-m-d H:i:s'))->sortable(true);
		$this->addField('created_by_id')->defaultValue($this->app->employee->id)->sortable(true);
		$this->addField('status')->enum($this->status)->sortable(true);

		//Delivered Option or shipping tracking code
		$this->addField('delivery_via');
		$this->addField('delivery_reference');
		$this->addField('shipping_address')->type('text');
		$this->addField('shipping_charge')->type('money');
		$this->addField('narration')->type('text');
		$this->addField('tracking_code')->type('text');


		$this->hasMany('xepan\commerce\Store_TransactionRow','store_transaction_id',null,'StoreTransactionRows');
		$this->hasMany('xepan\commerce\Store_TransactionRowCustomFieldValue','store_transaction_id',null,'StoreTransactionRows');
		
		$this->addExpression('item_quantity')->set(function($m,$q){
			$to_received = $m->refSQL('StoreTransactionRows')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$to_received]);
		})->sortable(true);

		$this->addExpression('toreceived')->set(function($m,$q){
			$to_received = $m->refSQL('StoreTransactionRows')
							->addCondition('status','ToReceived')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$to_received]);
		})->sortable(true);	

		$this->addExpression('received')->set(function($m,$q){
			$to_received = $m->refSQL('StoreTransactionRows')
							->addCondition('status','Received')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$to_received]);
		})->sortable(true);



		$this->addExpression('department')->set(function($m,$q){
			return $m->refSQL('jobcard_id')->fieldQuery('department');
		});

		$this->addExpression('jobcard_item')->set(function($m,$q){
			return $m->refSQL('jobcard_id')->fieldQuery('order_item_name');
		});
		// $this->addExpression('item_qty')->set(function($m,$q){
		// 	return $m->refSQL('StoreTransactionRows')->fieldQuery('quantity');
		// });

		$this->addExpression('related_contact_id')->set(function($m,$q){
			return  $m->add('xepan\commerce\Model_SalesOrder')
					->addCondition('id',$m->getElement('related_document_id'))
					->fieldQuery('contact_id');
		});

		$this->addExpression('contact_name')->set(function($m,$q){
			return $this->add('xepan\base\Model_Contact')
					->addCondition('id',$m->getElement('related_contact_id'))
					->fieldQuery('name');

		});
		$this->addExpression('organization')->set(function($m,$q){
			return $this->add('xepan\base\Model_Contact')
					->addCondition('id',$m->getElement('related_contact_id'))
					->fieldQuery('organization');

		});

		$this->addExpression('organization_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization]) OR trim([organization])="" ,[contact_name],[organization])',
						[
							'contact_name'=>$m->getElement('contact_name'),
							'organization'=>$m->getElement('organization')
						]
					);
		});

		$this->addExpression('related_document_no')->set(function($m,$q){
			$sales_order =  $m->add('xepan/commerce/Model_QSP_Master',['table_alias'=>'order_no']);
			$sales_order->addCondition('id',$m->getElement('related_document_id'));
			return $sales_order->fieldQuery('document_no');
		})->sortable(true);
	}
	
	function fromWarehouse($warehouse=false){
		if($warehouse)
			$this['from_warehouse_id'] = $warehouse->id;
		else
			return $this->ref('from_warehouse_id');
	}

	function toWarehouse($warehouse=false){
		if($warehouse)
			$this['to_warehouse_id'] = $warehouse->id;
		else
			return $this->ref('to_warehouse_id');
	}

	function transactionRow(){
		$this->ref('StoreTransactionRows');
	}

	function addItem($qsp_detail_id,$item_id=null,$qty,$jobcard_detail_id,$custom_fields=[],$status="ToReceived"){
		$new_item = $this->ref('StoreTransactionRows');
		$new_item['store_transaction_id'] = $this->id;
		$new_item['qsp_detail_id'] = $qsp_detail_id;
		$new_item['item_id'] = $item_id;
		$new_item['quantity'] = $qty;
		$new_item['jobcard_detail_id'] = $jobcard_detail_id;
		$new_item['status'] = $status;
		$new_item->save();
		if($custom_fields){
			$custom_array = json_decode($custom_fields,true);
			foreach ($custom_array as $department_id => $value) {
					// var_dump($value);
				foreach ($value as $custom_field_id => $value_array) {
					if(!is_array($value_array) or !is_numeric($custom_field_id)) continue;  
					$m = $this->add('xepan\commerce\Model_Store_TransactionRowCustomFieldValue');
					$m['customfield_generic_id'] = $custom_field_id; 
					
					if(!is_numeric($value_array['custom_field_value_id'])){
						$m['customfield_value_id']= 0;
					}else{
						$m['customfield_value_id']= $value_array['custom_field_value_id'];
					} 
					$m['store_transaction_row_id'] = $new_item->id;
					$m['custom_name'] = $value_array['custom_field_name'];
					$m['custom_value'] = $value_array['custom_field_value_name'];
					$m->save();
				}
			}
			
		}

		return $this;
	}

	function saleOrder(){
		if(!$this->loaded()){
			throw new \Exception("Transaction Not Loaded");
		}

		$sale_order = $this->add('xepan\commerce\Model_SalesOrder');
		$sale_order->addCondition('id',$this['related_document_id']);
		$sale_order->tryLoadAny();
		if(!$sale_order->loaded())
			return false;

		return $sale_order;
	}
}