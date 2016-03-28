<?php

namespace xepan\commerce;

class Model_QSP_Detail extends \xepan\base\Model_Table{
	public $table="qsp_detail";
	public $status = [];
	public $actions = [];
	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\QSP_Master','qsp_master_id');
		$this->hasOne('xepan\commerce\Item','item_id')->display(array('form'=>'xepan\commerce\Item'));

		$this->addField('price')->caption('Rate');
		$this->addField('quantity');
		$this->addExpression('amount_excluding_tax')->set($this->dsql()->expr('([0]*[1])',[$this->getElement('price'),$this->getElement('quantity')]))->type('money');

		$this->addField('tax_percentage');
		$this->addExpression('tax_amount')->set($this->dsql()->expr('([0]*[1]/100.00)',[$this->getElement('amount_excluding_tax'),$this->getElement('tax_percentage')]))->type('money');

		$this->addExpression('total_amount')->set(function($m,$q){
			return $q->expr('([0]+[1])',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);
		})->type('money');

		$this->addField('shipping_charge');
		$this->addField('narration');
		$this->addField('extra_info')->type('text'); // Custom Fields

		$this->addExpression('order_contact')->set($this->refSQL('qsp_master_id')->fieldQuery('contact'));

		//has many departmental status
		$this->hasMany('xepan\commerce\OrderItemDepartmentalStatus','qsp_detail_id');

		$this->addHook('beforeDelete',[$this,'deleteDepartmentalstatus']);

	}

	function deleteDepartmentalstatus(){
		$dept_status_asso = $this->ref('xepan\commerce\OrderItemDepartmentalStatus');

		foreach ($dept_status_asso as $single_asso) {
			$single_asso->delete();
		}
		
	}

	//CREATING DEPARTMENTAL ASSOCIATION FOR JOBCARD 
	function createDepartmentalAssociations(){
		
		$departments = $this->add('xepan\hr\Model_Department');

		if($this['extra_info']=="" or $this['extra_info'] == null){
			$assos_depts = $this->item()->getAssociatedDepartment();
			foreach ($assos_depts as $dp) {
				$custome_fields_array[$dp]=array();
			}
		}else{
			$custome_fields_array  = json_decode($this['extra_info'],true);
		}

		foreach ($departments as $dept) {
			if(isset($custome_fields_array[$dept->id])){
				// associate with department
				$this->addToDepartment($dept);
			// }else{
			// 	// remove association with department
			// 	if($this->getCurrentStatus($dept) != 'Waiting')
			// 		$this->removeFromDepartment($dept);
			// 	else
			// 		throw $this->exception('Job Has Been forwarded to department '. $dept['name'].' and cannot be removed');
			}
		}
	}

	function item(){
		if(!$this['item_id'])
			throw $this->exception("can't load the item ")
	                   ->addMoreInfo('item id not found', $type);
			
		return $this->ref('item_id');
	}

	function addToDepartment($department){
		
		$relation = $this->add('xepan\commerce\Model_OrderItemDepartmentalStatus')
					->addCondition('department_id',$department->id)
					->addCondition('qsp_detail_id',$this->id)
					;
		if(!$relation->count()->getOne()){
			$relation->tryLoadAny();
			$relation->saveAndUnload();
		}

	}

	function getCurrentStatus($department=false){

		$last_ds = $this->deptartmentalStatus()->addCondition('status','<>','Waiting');	
		$last_ds->_dsql()->order('id','desc');
		if($department){
			$last_ds->addCondition('department_id',$department->id);
		}
		$last_ds->tryLoadAny();

		if($last_ds->loaded())
			return $last_ds['status'];

		return "Waiting";
	}

	function deptartmentalStatus($department=false,$from_custom_fields=false){
		
		if($from_custom_fields){
			$return_array=array();
			$cf = json_decode($this['extra_info'],true);
			if(!$cf) return $return_array;
			foreach ($cf as $dept_id => $cfvalues_array) {
				if($dept_id == 'stockeffectcustomfield'){//check for the stockeffect customfields
					$return_array[] = array('department_id'=>$dept_id,'department'=>'stockeffectcustomfield','status'=>'');
				}else{					
					$m = $this->add('xepan\commerce\Model_OrderItemDepartmentalStatus');
					$m->addCondition('qsp_detail_id',$this->id);
					$m->addCondition('department_id',$dept_id);
					$m->tryLoadAny();
					$name = "";
					if($m['outsource_party_id'])
						$name = $m->ref('outsource_party_id')->get('name');
					$return_array[] = array('department_id'=>$dept_id,'department'=>$this->add('xHR/Model_Department')->load($dept_id)->get('name'),'status'=>$m['status'],'outsource_party_id'=>$m['outsource_party_id'],'outsource_party'=>$name);
				}
			}
			return $return_array;
		}else{
			$m = $this->add('xepan\commerce\Model_OrderItemDepartmentalStatus');
			$m->setOrder('production_level');
			$m->addCondition('qsp_detail_id',$this->id);

			if($department){
				$m->addCondition('department_id',$department->id);
				$m->tryLoadAny();
				if(!$m->loaded()) return false;
			}
			// Now add qty effected custom fields manually from json
			// $cf = json_decode($this['custom_fields'],true);
			// $m[] = array('department_id'=>null,'department'=>'stockeffectcustomfield','status'=>'');

			return $m;
		}
	}

	function removeFromDepartment($department){
		$relation = $this->ref('xepan\commerce\OrderItemDepartmentalStatus')->addCondition('department_id',$department->id);
		if($relation->tryLoadAny()->loaded())
			$relation->delete();
	}

	function nextDeptStatus($after_department=false){
		$dept_status = $this->deptartmentalStatus();
		
		foreach ($dept_status as $ds) {
				
			if($after_department){
				if($ds->department()->get('id') == $after_department->id) $after_department=false;
			}else{
				
				if($ds['status'] == 'Waiting'){
					return $ds;
				}
			}
		}
		return false;
	}


	function firstProductionDepartment(){
		$extra_info_array = json_decode($this['extra_info'],ture);
		$department_array = array_keys($extra_info_array);
		return $this->add('Model_Department')->addCondition('id',$department_array)->setOrder('production_level','asc')->setLimit(1);
	}
	
}