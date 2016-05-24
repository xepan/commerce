<?php 
 namespace xepan\commerce;
 class page_tax extends \xepan\base\Page{

	public $title='Vat & Tax';

	function init(){
		parent::init();

		/*Taxation */
		$tax = $this->add('xepan\commerce\Model_Taxation');
		$crud = $this->add('xepan\hr\CRUD',null,
						'taxation',
						['view/tax/grid']
					);
		if($crud->isEditing()){
			$crud->form->setLayout('view\form\tax');
		}
		$crud->setModel($tax,['name','percentage']);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(50);

		/*Taxation Rules*/
		$tax_rule = $this->add('xepan\commerce\Model_TaxationRule');
		$crud_rule = $this->add('xepan\hr\CRUD',null,'taxation_rule');
		$crud_rule->setModel($tax_rule);
		$crud_rule->grid->addQuickSearch(['name']);
		$crud_rule->grid->addPaginator(50);		
		

		// /*Taxation Rules Rows*/
		$tax_rule_row = $this->add('xepan\commerce\Model_TaxationRuleRow');
		$crud_rule_row = $this->add('xepan\hr\CRUD',null,'taxation_rule_row');
		$crud_rule_row->setModel($tax_rule_row);
		$crud_rule_row->grid->addPaginator(50);

		$misc_config = $this->app->epan->config;
		$misc_tax_on_shipping = $misc_config->getConfig('TAX_ON_SHIPPING');
		$misc_tax_on_discounted_price = $misc_config->getConfig('TAX_ON_DISCOUNTED_PRICE');
		$misc_tax_as_per = $misc_config->getConfig('TAX_AS_PER');

		/*taxation_configuration*/
		$form = $this->add('Form',null,'taxation_configuration');
		$field_tax_on_shipping = $form->addField('checkbox','tax_on_shipping')->set($misc_tax_on_shipping);
		$field_tax_on_discounted_price = $form->addField('checkbox','tax_on_discounted_price')->set($misc_tax_on_discounted_price);
		$field_tax_as_per = $form->addField('DropDown','tax_as_per')->setValueList(['billing'=>"Billing",'shipping'=>'Shipping'])->set($misc_tax_as_per);

		$form->addSubmit('Save');

		if($form->isSubmitted()){
			$misc_config->setConfig('TAX_ON_SHIPPING',$form['tax_on_shipping']?:0,'commerce');
			$misc_config->setConfig('TAX_ON_DISCOUNTED_PRICE',$form['tax_on_discounted_price']?:0,'commerce');
			$misc_config->setConfig('TAX_AS_PER',$form['tax_as_per'],'commerce');

			$form->js()->univ()->successMessage('Saved Successfully')->execute();
		}

		/*Shipping Rules*/
		$shipping_rule  = $this->add('xepan\commerce\Model_ShippingRule');
		$crud_shipping_rule = $this->add('xepan\hr\CRUD',null,'shippingrule');
		$crud_shipping_rule->setModel($shipping_rule);
		$crud_shipping_rule->grid->addPaginator(50);

		$crud_shipping_rule->grid->add('VirtualPage')
			->addColumn('Rules')
			->set(function($page){
				$shipping_rule_id = $_GET[$page->short_name.'_id'];
				
				$shipping_rule  = $page->add('xepan\commerce\Model_ShippingRuleRow');
				$shipping_rule->addCondition('shipping_rule_id',$shipping_rule_id);

				$crud_shipping_rule = $page->add('xepan\hr\CRUD');
				$crud_shipping_rule->setModel($shipping_rule);
				$crud_shipping_rule->grid->addPaginator(50);

		});

	}

	function defaultTemplate(){
		return ['page/taxation'];
	}
}



/*Country state Import from XML*/
// $country_xml = file_get_contents('../country.xml');
// 		$xml = simplexml_load_string($country_xml, "SimpleXMLElement", LIBXML_NOCDATA);
// 		$json = json_encode($xml);
// 		$array = json_decode($json,TRUE);

// 		$state_xml = file_get_contents('../state.xml');
// 		$xml = simplexml_load_string($state_xml, "SimpleXMLElement", LIBXML_NOCDATA);
// 		$state_json = json_encode($xml);
// 		$state_array = json_decode($state_json,TRUE);

// 		// echo "<pre>";
// 		// print_r();
// 		//Country Insert
// 		$new_country_array = [];
// 		$array = $array['country'];
// 		foreach ( $array as $junk) {
// 			$country = $this->add('xepan\base\Model_Country');
// 			$country['name'] = $junk['name'];
// 			$country['iso_code'] = $junk['iso_code'];
// 			$country->save();
// 			$new_country_array[$junk['country_id']] = ['new_id'=>$country->id];
// 		}
		
// 		$state_array = $state_array['state_province'];
// 		foreach ($state_array as $junk) {
// 			$state = $this->add('xepan\base\Model_State');
// 			$state['name'] = $junk['name'];
// 			$state['abbreviation'] = $junk['abbreviation'];
// 			$state['country_id'] = $new_country_array[$junk['country_id']]['new_id'];
// 			$state->save();
// 		}  