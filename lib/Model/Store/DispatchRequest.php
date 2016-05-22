<?php
namespace xepan\commerce;

class Model_Store_DispatchRequest extends \xepan\commerce\Model_Store_Transaction{
	public $status = ['ToReceived','Received','Dispatch','ReceivedByParty'];
	public $actions=[
				'ToReceived'=>['view','edit','delete','receive'],
				'Received'=>['view','edit','delete','dispatch'],
				'Dispatch'=>['view','edit','delete','receivedByParty'],
			];
	function init(){
		parent::init();
		
		$this->addCondition('document_type','Dispatch');
	}

	function receive(){

		$tra_row=$this->add('xepan\commerce\Model_Store_TransactionRow');
		$tra_row->addCondition('store_transaction_id',$this->id);
		$tra_row->tryLoadAny();
		// throw new \Exception($tra_row->id, 1);
		
		$old_jb=$this->add('xepan\production\Model_Jobcard_Detail');
		$old_jb->addCondition('id',$tra_row['jobcard_detail_id']);
		$old_jb->tryLoadAny();
		
		if(!$old_jb->loaded())
			throw new \Exception("jobcard detail not found");
			
		$new_jd = $this->add('xepan\production\Model_Jobcard_Detail');
		$new_jd['quantity'] = $old_jb['quantity']; 
		$new_jd['parent_detail_id'] = $old_jb['parent_detail_id']; 
		$new_jd['jobcard_id'] = $old_jb['jobcard_id'];
		$new_jd['status'] = "ReceivedByDispatch";
		$new_jd->save();

		$this['status']="Received";
		$this->save();
		return true;
		
	}
	function dispatch(){
		$this->api->redirect('xepan_commerce_store_deliveryManagment',['transaction_id'=>$this->id]);
	}

	function receivedByParty(){
		$this['status']='ReceivedByParty';
		$this->saveAndUnload();
	}

	function saleOrder(){
		if(!$this->loaded()){
			throw new \Exception("sale order not found");
		}

		$sale_order = $this->add('xepan\commerce\Model_SalesOrder');
		$sale_order->tryLoadBy('id',$this['related_document_id']);
		if(!$sale_order->loaded())
			return false;

		return $sale_order;
	}
}