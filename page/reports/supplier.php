<?php

namespace xepan\commerce;

class page_reports_supplier extends \xepan\commerce\page_reports_reportsidebar{
	public $title = 'Supplier Report';

	function init(){
		parent::init();

		$toggle_button = $this->add('Button',null,'toggle')->set('Show/Hide form')->addClass('btn btn-primary btn-sm');
		$form_view = $this->add('xepan\commerce\View_Reports_FilterForm',null,'filterform');
		$this->js(true,$form_view->js()->hide());
		$toggle_button->js('click',$form_view->js()->toggle());
		
		$this->add('View',null,'view',null)->set('haha');	

	}
}