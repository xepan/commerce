<?php

namespace xepan\commerce;

class Model_Store_Transaction extends \xepan\base\Model_Table{
	public $table="store_transaction";
	// public $acl=false;
	public $status = ['ToReceived','Received'];
	public $actions=[
				'ToReceived'=>['view','edit','delete','receive'],
			];
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','from_warehouse_id');
		$this->hasOne('xepan\commerce\Store_Warehouse','to_warehouse_id');
		$this->hasOne('xepan\production\Jobcard','jobcard_id');
		$this->addField('type');
		$this->addCondition('type','Store_Transaction');
		
		$this->addField('related_document_id')->sortable(true); //Sale Ordre/Purchase
		$this->addField('document_type'); //Purchase/Sale/Dispatch/Deliver
		$this->addField('created_at')->defaultValue(date('Y-m-d'))->sortable(true);
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

	function addItem($qsp_detail_id,$qty,$jobcard_detail_id,$custom_fields,$customfield_value,$status="ToReceived"){
		$new_item = $this->ref('StoreTransactionRows');
		$new_item['store_transaction_id'] = $this->id;
		$new_item['qsp_detail_id'] = $qsp_detail_id;
		$new_item['quantity'] = $qty;
		$new_item['jobcard_detail_id'] = $jobcard_detail_id;
		$new_item['customfield_generic_id'] = $custom_fields;
		$new_item['customfield_value_id'] = $customfield_value ;
		$new_item['status'] = $status;
		$new_item->save();

		return $this;
	}

	function page_receive($page){
		$form = $page->add('Form');
		$jobcard_field = $form->addField('hidden','store_transaction_row');
		$form->addSubmit('Receive');

		$grid_jobcard_row = $page->add('xepan\hr\Grid',['action_page'=>'xepan_production_jobcard'],null,['view/jobcard/transactionrow']);
		$grid_jobcard_row->addSelectable($jobcard_field);

		$tra_row=$this->add('xepan\commerce\Model_Store_TransactionRow');
		$tra_row->addCondition('store_transaction_id',$this->id);
		$tra_row->addCondition('status',"ToReceived");
		$grid_jobcard_row->setModel($tra_row);

		$grid_jobcard_row->addHook('formatrow',function($m){
			$array = json_decode($m['extra_info']?:"[]",true);
			$cf_html = " "; 

			foreach ($array as $department_id => &$details) {
				$department_name = $details['department_name'];
				$cf_list = $this->add('CompleteLister',null,'extra_info',['view\qsp\extrainfo']);
				$cf_list->template->trySet('department_name',$department_name);
				unset($details['department_name']);
				
				$cf_list->setSource($details);

				$cf_html  .= $cf_list->getHtml();	
			}		
			$this->current_row_html['extra_info'] = $cf_html . $m['narration'];
		});

		if($form->isSubmitted()){
			//doing jobcard detail/row received
			foreach (json_decode($form['store_transaction_row']) as $transaction_row_id) {
				$tran_row_model = $this->add('xepan\commerce\Model_Store_TransactionRow')->load($transaction_row_id);
				$tran_row_model->receive();

				//create one new jobcard detail with status ReceivedByDispatch
				$job_detail = $this->add('xepan\production\Model_Jobcard_Detail');
				$job_detail->addCondition('id',$tran_row_model['jobcard_detail_id']);
				$job_detail->tryLoadAny();
				
				$new_jd = $this->add('xepan\production\Model_Jobcard_Detail');
				$new_jd['quantity'] = $job_detail['quantity']; 
				$new_jd['parent_detail_id'] = $job_detail['parent_detail_id']; 
				$new_jd['jobcard_id'] = $job_detail['jobcard_id'];
				$new_jd['status'] = "ReceivedByDispatch";
				$new_jd->save();
			}
			
			$this->receive();
			return $form->js()->univ()->successMessage('Received Successfully');
		}
	}

	function receive(){
		$this['status']="Received";
		$this->save();
		return true;		
	}
}