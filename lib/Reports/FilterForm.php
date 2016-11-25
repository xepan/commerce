<?php

namespace xepan\commerce;

class Reports_FilterForm extends \Form{
	public $extra_field;
	public $status_array;
	function init(){
		parent::init();

		$this->setLayout('reports\form');
		$this->date_range_field = $this->addField('DateRangePicker','date_range')
								 ->setStartDate($this->app->now)
								 ->setEndDate($this->app->now)
								 ->getBackDatesSet();
	    $this->addField('autocomplete/Basic','contact')->setModel('xepan\base\Contact');
	    $this->addField('from_amount');
	    $this->addField('to_amount');
		
		if($this->extra_field){
			$this->addField('xepan\base\DropDown','status')->setValueList($this->status_array)->setEmptyText('Please Select');
			$this->addField('xepan\base\DropDown','order')->setValueList(['desc'=>'Highest','asc'=>'Lowest'])->setEmptyText('Please Select');
		}else{
			$this->layout->template->tryDel('extra_field_wrapper');
		}

		$this->addSubmit('Filter')->addClass('btn btn-primary btn-block');
	}

	function validateFields(){

		if(($this['status'] == null AND $this['order'] !=null) OR ($this['status'] != null AND $this['order'] ==null))
			$this->displayError('status','Please select order and status both');
			

		if($this['from_amount'] != null){
			if(!is_numeric($this['from_amount']))
				$this->displayError('from_amount',$this['from_amount'].' is not a numeric value');
		}
		
		if($this['to_amount'] != null){
			if(!is_numeric($this['to_amount']))
				$this->displayError('to_amount',$this['to_amount'].' is not a numeric value');
		}	
		
		return $this;
	}

	function reloadView($view){
		$from_date = $this->date_range_field->getStartDate();
    	$to_date = $this->date_range_field->getEndDate();						
					
		$this->js(null,$view->js()
			 ->reload(
				[
					'from_date'=>$from_date,
					'to_date'=>$to_date,
					'contact_id'=>$this['contact'],
					'from_amount'=>$this['from_amount'],
					'to_amount'=>$this['to_amount'],
					'status'=>$this['status'],
					'order'=>$this['order']
				]))->univ()->successMessage('wait ... ')->execute();
	}
}