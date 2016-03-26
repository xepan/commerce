<?php
/*
	This Model used for Jobcard, it manage the qspdetails item departmental wise status
*/
namespace xepan\commerce;

class Model_OrderItemDepartmentalStatus extends \xepan\base\Model_Table{
	public $table = "order_item_departmental_status";
	public $status=['Waiting'];
	public $actions=[
		'*'=>['add','view','edit','delete']
		];
	public $acl=false; 

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\QSP_Detail','qsp_detail_id');
		$this->hasOne('xepan\hr\Department','department_id');
		
		$this->addExpression('Quantity')->set(function($m,$q){
			return $m->refSQL('qsp_detail_id')->fieldQuery('quantity');
		});
		
		$this->addField('status')->defaultValue('Waiting');
		$this->addField('is_open')->type('boolean')->defaultValue(true)->system(true);
		// status of previous department jobcard .. if any or null

		$this->addExpression('production_level')->set(function($m,$q){
			return $m->refSQL('department_id')->fieldQuery('production_level');
		});

		$this->addExpression('order_no')->set($this->refSQL('qsp_detail_id')->fieldQuery('qsp_master_id'));

		$this->addExpression('qsp_order_contact')->set($this->refSQL('qsp_detail_id')->fieldQuery('order_contact'));


		// $this->addExpression('previous_status')->set(function($m,$q){
		// 	return "'Todo'";
		// 	// my departments
		// 	// my previous departments : leftJOin
		// 	// job cards of my same orderitem_id from previous departments
		// 	// limit 1
		// 	// 
		// 	return $m->refSQL('xProduction/JobCard')->_dsql()->limit(1)->del('fields')->field('status');
		// });

		// hasMany JobCards
		$this->hasMany('xepan\production\Jobcard','order_item_departmental_status_id');

		$this->addHook('beforeDelete',[$this,'deleteJobcard']);
	}

	function deleteJobcard(){
		$jobcards = $this->ref('xepan\production\Jobcard');
		foreach ($jobcards as $jobcard) {
				$jobcard->delete();
			}	
	}

	function close(){
		$this['is_open']= false;
		$this->save();
	}

	function createJobCardFromOrder(){		
		$new_job_card = $this->add('xepan\production\Model_Jobcard');
		$new_job_card->createFromOrder($this->ref('qsp_detail_id'),$this);
		$this['status']='Sent To '. $this['department'];
		$this->save();
		return $new_job_card;
	}

	function receive_to_DELETE(){
		// create job card for this department and this orderitem_id;
		$jobcard_model=$this->add($this->department()->defaultDocument());
		$jobcard_model->addCondition('orderitem_departmental_status_id',$this->id);
		$jobcard_model->addCondition('orderitem_id',$this['orderitem_id']);
		$jobcard_model->addCondition('department_id',$this['department_id']);
		$jobcard_model->tryLoadAny();
		if($jobcard_model->loaded())
			throw $this->exception('Already Recieved and Job Card Created');
		
		$jobcard_model->receive();

		$this['status']='Received By '. $this['department'];
		$this->save();

		// jiska status ... received hoga
		// agar previous department hai to
			// uske job card ka status complete ka do
		// creatre log/communication entry
	}

	function department(){
		return $this->ref('department_id');
	}

	function orderItem(){
		return $this->ref('orderitem_id');
	}

	function outSourceParty($party=null){
		if(!$party){
			$t= $this->ref('outsource_party_id');
			if($t->loaded()) return $t;
			return false;
		}else{
			$this['outsource_party_id'] = $party->id;
			$this->save();
			return $party;
		}
	}

	function setStatus($status){
		$this['status']=$status;
		$this->save();
	}

	function jobCardNotCreated(){
		return $this['status'] == 'Waiting';
	}

}