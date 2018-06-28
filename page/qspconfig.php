<?php 
 namespace xepan\commerce;
 class page_qspconfig extends \xepan\commerce\page_configurationsidebar{

	public $title='QSP Config';

	function init(){
		parent::init();

		$tab = $this->add('Tabs');
		$tab1 = $tab->addTab('General Config');
		$tabc = $tab->addTab('QSP Cancel Reason');
		
		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->add('xepan\hr\Controller_ACL');
		$qsp_config->tryLoadAny();

		$apply = "tax_on_discounted_amount";
		if($qsp_config['discount_on_taxed_amount']){
			$apply = "discount_on_taxed_amount";
		}

		$form = $tab1->add('Form');
		$form->addField('checkbox','discount_per_item')->set($qsp_config['discount_per_item']);
		$form->addField('Radio','apply')->setValueList(
									[
										'discount_on_taxed_amount'=>'Discount On Taxed Amount',
										'tax_on_discounted_amount'=>'Tax On Discounted Amount'
									])->validate('required')->set($apply);
		$form->addField('quotation_serial')->set($qsp_config['quotation_serial']);
		$form->addField('sale_order_serial')->set($qsp_config['sale_order_serial']);
		$form->addField('sale_invoice_serial')->set($qsp_config['sale_invoice_serial']);
		$form->addField('checkbox','show_shipping_address_in_pos')->set($qsp_config['show_shipping_address_in_pos']);

		$form->addSubmit('Save')->addClass('btn btn-primary');
		if($form->isSubmitted()){			

			$qsp_config['discount_per_item'] = $form['discount_per_item'];
			
			if($form['apply'] == "discount_on_taxed_amount"){
				$qsp_config['tax_on_discounted_amount'] = 0;
				$qsp_config['discount_on_taxed_amount'] = 1;
			}else{
				$qsp_config['tax_on_discounted_amount'] = 1;
				$qsp_config['discount_on_taxed_amount'] = 0;
			}
			$qsp_config['quotation_serial'] = $form['quotation_serial'];
			$qsp_config['sale_order_serial'] = $form['sale_order_serial'];
			$qsp_config['sale_invoice_serial'] = $form['sale_invoice_serial'];
			$qsp_config['show_shipping_address_in_pos'] = $form['show_shipping_address_in_pos'];
			$qsp_config->save();

			$msg = "Discount on QSP";
			if($qsp_config['discount_per_item']){
				$msg = "Discount Per Item Wise";
			}

			$qsp_config->app->employee
            ->addActivity("Qsp Config Updated")
			->notifyWhoCan(' ',' ',$qsp_config);
			$form->js(null,$form->js()->reload())->univ()->successMessage('QSP Config Updated')->execute();
		}

		$crud = $tabc->add('CRUD');
		$crud->setModel('xepan\commerce\Model_Config_QSPCancelReason');

	}
} 