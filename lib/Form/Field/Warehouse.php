<?php

namespace xepan\commerce;
class Form_Field_Warehouse extends \xepan\base\Form_Field_DropDown {

	public $validate_values = false;
	public $id_field = 'id';
	public $title_field = 'name';
	public $include_status = 'Active'; // all, no condition
	public $contact_class = 'xepan\commerce\Model_Store_Warehouse';
	public $setCurrent=false;

	function init(){
		parent::init();
		$this->setEmptyText('Please Select');
	}

	function setType($type=null){
		if($type) $this->addCondition('type',$type);
		return $this;
	}

	function setContactType($contact_type){
		$this->contact_class = $contact_type;
		return $this;
	}

	function setIdField($id_field){
		$this->id_field = $id_field;
		return $this;
	}

	function setTitleField($title_field){
		$this->title_field = $title_field;
		return $this;
	}

	function includeAll(){
		$this->include_status=null;
		return $this;
	}

	function includeStatus($status){
		$this->include_status = $status;
		return $this;
	}

	function setCurrent(){
		$this->setCurrent=true;
		return $this;
	}


	function recursiveRender(){
		if(!($employee_list=$this->app->recall('Form_Field_Employee_Model_'.$this->include_status,false))){
			$contact = $this->add($this->contact_class);
			if($this->include_status) $contact->addCondition('status',$this->include_status);
			$employee_list = $contact->getRows();
			$this->app->memorize('Form_Field_Employee_Model_'.$this->include_status, $employee_list);
		}
		$this->setValueList(array_combine(array_column($employee_list, $this->id_field), array_column($employee_list,$this->title_field)));
		if($this->setCurrent) $this->set($this->app->employee->id);
		parent::recursiveRender();
	}
}