<?php

namespace xepan\commerce;

class Form_Field_Warehouse extends \xepan\base\Form_Field_DropDown {

	public $validate_values = false;
	public $id_field = 'id';
	public $title_field = 'warehouse';
	public $include_status = null; // all, no condition
	public $model_class = 'xepan\commerce\Model_Store_Warehouse';
	public $setCurrent=false;
	public $skip_branch_condition=false;
	function init(){
		parent::init();

		$this->setEmptyText('Please Select');
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

	function recursiveRender(){
		
		// if(!($warehouse_list=$this->app->recall('Form_Field_Warehouse_Model_'.$this->include_status,false))){
			$contact = $this->add($this->model_class);
			if($this->include_status) $contact->addCondition('status',$this->include_status);

			if(!$this->skip_branch_condition){
				$tra_model = $this->add('xepan\commerce\Model_Store_Transaction');
				$acl_model = $tra_model->add('xepan\hr\Controller_ACL');
				if($acl_model->isBranchRestricted()){
					$contact->addCondition('branch_id',$this->app->branch->id);
				}

			}

			$warehouse_list = $contact->getRows();
			// $this->app->memorize('Form_Field_Warehouse_Model_'.$this->include_status, $warehouse_list);
		// }

		$this->setValueList(array_combine(array_column($warehouse_list, $this->id_field), array_column($warehouse_list,$this->title_field)));
		parent::recursiveRender();
	}

}