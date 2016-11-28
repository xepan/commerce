<?php 
 namespace xepan\commerce;
 class page_shippingrule extends \xepan\commerce\page_configurationsidebar{

	public $title='Shipping Rule';

	function init(){
		parent::init();

		/*Shipping Rules*/
		$shipping_rule  = $this->add('xepan\commerce\Model_ShippingRule');
		$crud_shipping_rule = $this->add('xepan\hr\CRUD',null,null,['view\tax\shippingrule']);//,null,'shippingrule',['view\tax\shippingrule']);
		$crud_shipping_rule->setModel($shipping_rule,['country_id','country','state_id','state','name','based_on']);
		$crud_shipping_rule->grid->addPaginator(50);
		$crud_shipping_rule->grid->addQuickSearch(['name']);
		$crud_shipping_rule->add('xepan\base\Controller_MultiDelete');

		$country_id = $this->app->stickyGET('country_id');
		if($country_id){	
			$field_state = $crud_shipping_rule->form->getElement('state_id');					
			$field_state->getModel()->addCondition('country_id',$country_id);
		}

		if($crud_shipping_rule->isEditing()){
			$form = $crud_shipping_rule->form;
			$field_country = $crud_shipping_rule->form->getElement('country_id');

			$field_country->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$field_country->js()->val()]));
		}

		$crud_shipping_rule->grid->add('VirtualPage')
			->addColumn('Rules')
			->set(function($page){
				$shipping_rule_id = $_GET[$page->short_name.'_id'];
				
				$shipping_rule  = $page->add('xepan\commerce\Model_ShippingRuleRow');
				$shipping_rule->addCondition('shipping_rule_id',$shipping_rule_id);

				$crud_shipping_rule = $page->add('xepan\base\CRUD',null,null,['view\tax\shippingrulerow']);
				$crud_shipping_rule->setModel($shipping_rule);
				$crud_shipping_rule->grid->addPaginator(50);
				$crud_shipping_rule->grid->addQuickSearch(['shipping_rule']);
				$crud_shipping_rule->add('xepan\base\Controller_MultiDelete');
		});

	}
}