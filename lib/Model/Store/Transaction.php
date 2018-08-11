<?php

namespace xepan\commerce;

class Model_Store_Transaction extends Model_Store_TransactionAbstract{
	
	public $status = ['ToReceived','Received'];
	public $actions=[
				'ToReceived'=>['view','details','edit','delete','receive'],
				'Received'=>['view','details','edit','delete'],
			];
	public $acl_type = "Store_Transaction";
	function init(){
		parent::init();

		
		// $this->addCondition('type','Store_Transaction');
		
	}

	function page_details($page){
		$row_model = $page->add("xepan\commerce\Model_Store_TransactionRow");
		$row_model->addCondition('store_transaction_id',$this->id);

		$crud = $page->add('xepan\base\CRUD',['allow_add'=>false]);
		$crud->setModel($row_model,['item_id','quantity','serial_nos','narration','extra_info'],['item','quantity','status','extra_info','serial_nos','narration','type','item_qty_unit','']);

	}

	function page_receive($page){
		
		$form = $page->add('Form');
		$jobcard_field = $form->addField('hidden','store_transaction_row');
		$form->addSubmit('Receive');

		$grid_jobcard_row = $page->add('xepan\hr\Grid',['action_page'=>'xepan_production_jobcard']);
		$grid_jobcard_row->addSelectable($jobcard_field);
		
		$tra_row=$this->add('xepan\commerce\Model_Store_TransactionRow');
		$tra_row->addCondition('store_transaction_id',$this->id);
		$tra_row->addCondition('status',"ToReceived");
		$grid_jobcard_row->setModel($tra_row,['item_name','quantity','extra_info','serial_nos','narration','from_warehouse','created_at']);

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
				
				if($job_detail->loaded()){
					$new_jd = $this->add('xepan\production\Model_Jobcard_Detail');
					$new_jd['quantity'] = $job_detail['quantity'];
					$new_jd['parent_detail_id'] = $job_detail['parent_detail_id']; 
					$new_jd['jobcard_id'] = $job_detail['jobcard_id'];
					$new_jd['status'] = "ReceivedByDispatch";
					$new_jd->save();
				}
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