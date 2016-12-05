<?php
namespace xepan\commerce;

class Model_Store_DispatchRequest extends Model_Store_TransactionAbstract{
	
	public $status = ['ToReceived','Received','Dispatch','ReceivedByParty'];
	public $actions=[
				'ToReceived'=>['view','edit','delete','receive'],
				'Received'=>['view','edit','delete','dispatch'],
				'Dispatch'=>['view','edit','delete','receivedByParty'],
			];
	function init(){
		parent::init();
		
		$this->addCondition('type','Store_DispatchRequest');
	}

	function receive(){

		$tra_row=$this->add('xepan\commerce\Model_Store_TransactionRow');
		$tra_row->addCondition('store_transaction_id',$this->id);
		$tra_row->tryLoadAny();
		
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
		$this->app->employee
            ->addActivity("Jobcard no. '".$this['id']."' recieved successfully by '".$this['department']."' department ", $this->id/* Related Document ID*/, null/*Related Contact ID*/,null,null,"xepan_production_jobcarddetail&document_id=".$this->id."")
            ->notifyWhoCan('dispatch','Received',$this);
		$this->save();

		$tra_row->receive();
		return true;
		
	}

	// function dispatch(){
	// 	$this->api->redirect('xepan_commerce_store_deliveryManagment',['transaction_id'=>$this->id]);
	// 	$this->app->employee
 //            ->addActivity("Jobcard no .'".$this['id']."' successfully send to dispatched", $this->id/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_production_jobcarddetail&document_id=".$this->id."")
 //            ->notifyWhoCan('receivedByParty','Dispatch',$this);
	// }

	function receivedByParty(){
		$this['status']='ReceivedByParty';
		$this->saveAndUnload();
	}

}