<?php
namespace xepan\commerce;
class page_warehousemanagment extends \xepan\commerce\page_configurationsidebar{
	public $title="Store Warehouse";
	function init(){
		parent::init();
		
		$crud = $this->add('xepan\hr\CRUD');
		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->layout([
						'first_name'=>'Store Warehouse Management~c1~12',
						'country_id~Country'=>'c2~4',
						'state_id~State'=>'c3~4',
						'city'=>'c4~4',
						'address'=>'c5~12',
						'pin_code'=>'c6~4',
						'organization'=>'c7~4',
						'branch_id~Branch'=>'c8~4',
						'FormButtons~&nbsp;'=>'c9~12'
					]);
		}
		$crud->grid->addPaginator(10);
		$crud->grid->addQuickSearch(['first_name']);

		$crud->setModel('xepan\commerce\Store_Warehouse',['first_name','country_id','state_id','city','address','pin_code','organization','branch_id'],['first_name','country','state','city','organization','address','pin_code','branch_id']);
		$store_country = $crud->form->getElement('country_id');
		$store_state = $crud->form->getElement('state_id');
		$store_state->dependsOn($store_country);
		
		// if($this->app->stickyGET('country_id'))
		// 	$store_state->getModel()->addCondition('country_id',$_GET['country_id'])->setOrder('name','asc');
		// 	$store_country->js('change',$store_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$store_state->name]),'country_id'=>$store_country->js()->val()]));
	}
}