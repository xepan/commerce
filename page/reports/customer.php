<?php

namespace xepan\commerce;

class page_reports_customer extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'Customer Report';

	function init(){
		parent::init();
		
		$toggle_button = $this->add('Button',null,'toggle')->set('Show/Hide form')->addClass('btn btn-primary btn-sm');
		$form = $this->add('xepan\commerce\Reports_FilterForm',null,'filterform');
		$this->js(true,$form->js()->hide());
		$toggle_button->js('click',$form->js()->toggle());
		
		$this->add('View',null,'view',null)->set('haha');	
	}

	function defaultTemplate(){
		return ['reports\pagetemplate'];
	}
}