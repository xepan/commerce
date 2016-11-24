<?php

namespace xepan\commerce;

class Reports_FilterForm extends \Form{
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
		$this->addSubmit('Filter')->addClass('btn btn-primary btn-block');
	}

	function validateFields(){

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
					'to_amount'=>$this['to_amount']
				]))->univ()->successMessage('wait ... ')->execute();
	}
}