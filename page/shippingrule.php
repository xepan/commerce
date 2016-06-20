<?php 
 namespace xepan\commerce;
 class page_shippingrule extends \xepan\commerce\page_configurationsidebar{

	public $title='Shipping Rule';

	function init(){
		parent::init();

		/*Shipping Rules*/
		$shipping_rule  = $this->add('xepan\commerce\Model_ShippingRule');
		$crud_shipping_rule = $this->add('xepan\hr\CRUD',null,null,['view\tax\shippingrule']);//,null,'shippingrule',['view\tax\shippingrule']);
		$crud_shipping_rule->setModel($shipping_rule);
		$crud_shipping_rule->grid->addPaginator(50);



		$crud_shipping_rule->grid->add('VirtualPage')
			->addColumn('Rules')
			->set(function($page){
				$shipping_rule_id = $_GET[$page->short_name.'_id'];
				
				$shipping_rule  = $page->add('xepan\commerce\Model_ShippingRuleRow');
				$shipping_rule->addCondition('shipping_rule_id',$shipping_rule_id);

				$crud_shipping_rule = $page->add('xepan\hr\CRUD',null,null,['view\tax\shippingrulerow']);
				$crud_shipping_rule->setModel($shipping_rule);
				$crud_shipping_rule->grid->addPaginator(50);
		});

	}
}