<?php
namespace xepan\commerce;
class page_store_warehouse extends \xepan\base\Page{
	public $title="Store Warehouse";
	function init(){
		parent::init();

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/store/warehouse-grid']);
		$crud->form->setLayout('view\store\form\warehouse');
		$crud->grid->addPaginator(10);
		$crud->grid->addQuickSearch(['first_name']);

		$crud->setModel('xepan\commerce\Store_Warehouse',['first_name','country_id','state_id','city','address','pin_code','organization'],['first_name','country','state','city','organization']);
		$store_country = $crud->form->getElement('country_id');
		$store_state = $crud->form->getElement('state_id');
		
		if($this->app->stickyGET('country_id'))
			$store_state->getModel()->addCondition('country_id',$_GET['country_id'])->setOrder('name','asc');
			$store_country->js('change',$store_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$store_state->name]),'country_id'=>$store_country->js()->val()]));
	}
}