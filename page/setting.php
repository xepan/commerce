<?php
namespace xepan\commerce;
class page_setting extends \xepan\commerce\page_configurationsidebar{

	public $title="General Settings";
	function init(){
		parent::init();

		
		$crud=$this->add('xepan\hr\CRUD',null,'currency_view',['view/setting/currency-grid']);
		$crud->setModel('xepan\commerce\Currency',['icon','name','value','status']);

	}
	
	function defaultTemplate(){
		return ['page/setting'];
	}
}