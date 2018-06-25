<?php

namespace xepan\commerce;

class View_EasySetupWizard extends \View{
	function init(){
		parent::init();

		/**
		............. Taxation System ...............
		*/
		if($_GET[$this->name.'_set_tax']){
			$this->js(true)->univ()->frameURL("Taxation System",$this->app->url('xepan_commerce_tax'));
		}

		$isDone = false;

		$action = $this->js()->reload([$this->name.'_set_tax'=>1]);

			if($this->add('xepan\commerce\Model_TaxationRule')->count()->getOne() > 0){
				$isDone = true;
				$action = $this->js()->univ()->dialogOK("Already have Data",' You have already set taxation rules, visit page ? <a href="'. $this->app->url('xepan_commerce_tax')->getURL().'"> click here to go </a>');
			}

		$tax_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Specify The Tax Rules')
			->setMessage('Specify tax to particular item/product, and add taxes according to norms of organization.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		
		/**
		............. Shipping Rules ...............
		*/
		if($_GET[$this->name.'_set_shipping_rule']){
			$this->js(true)->univ()->frameURL("Shipping Rule System",$this->app->url('xepan_commerce_shippingrule'));
		}

		$isDone = false;

		$action = $this->js()->reload([$this->name.'_set_shipping_rule'=>1]);

			if($this->add('xepan\commerce\Model_ShippingRuleRow')->count()->getOne() > 0){
				$isDone = true;
				$action = $this->js()->univ()->dialogOK("Already have Data",' You have already set shipping rules, visit page ? <a href="'. $this->app->url('xepan_commerce_shippingrule')->getURL().'"> click here to go </a>');
			}

		$shipping_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Specify The Shipping Rules')
			->setMessage('Specify the shipping rules with its detail (like amount, days etc.) according countries shipping services.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		
		/**
		............. Unit Conversion Configuration ...............
		*/
		if($_GET[$this->name.'_set_units']){
			$this->js(true)->univ()->frameURL("Unit Conversion System",$this->app->url('xepan_commerce_unit'));
		}

		$isDone = false;

		$action = $this->js()->reload([$this->name.'_set_units'=>1]);

			if($this->add('xepan\commerce\Model_UnitConversion')->count()->getOne() > 0){
				$isDone = true;
				$action = $this->js()->univ()->dialogOK("Already have Data",' You have already set unit conversion, visit page ? <a href="'. $this->app->url('xepan_commerce_unit')->getURL().'"> click here to go </a>');
			}

		$unit_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Specify The UnitGroup, Unit & Unit Conversion')
			->setMessage('Firstly specify unitgroup then specify unit. After that specify unit conversions')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		
		/**
			Custom Field
		*/
		if($_GET[$this->name.'_customfield']){
			$this->js(true)->univ()->frameURL("Item Custom Field",$this->app->url('xepan_commerce_customfield'));
		}
		$isDone = false;
		$action = $this->js()->reload([$this->name.'_customfield'=>1]);
		if($this->add('xepan\commerce\Model_Item_CustomField')->count()->getOne() > 0){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have already added Custom Field, visit page ? <a href="'. $this->app->url('xepan_commerce_customfield')->getURL().'"> click here to go </a>');
		}
		$this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Item Custom Fields')
			->setMessage('Item Specific selectable value\'s for customer')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);	
		// end of custom fields

		/**
			Specification Field
		*/
		if($_GET[$this->name.'_specification']){
			$this->js(true)->univ()->frameURL("Specification for Item",$this->app->url('xepan_commerce_specification'));
		}
		$isDone = false;
		$action = $this->js()->reload([$this->name.'_specification'=>1]);
		if($this->add('xepan\commerce\Model_Item_Specification')->count()->getOne() > 0){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have already added Specification, visit page ? <a href="'. $this->app->url('xepan_commerce_specification')->getURL().'"> click here to go </a>');
		}
		$this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Item Specification')
			->setMessage('Specification, that are fixed for item')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		// end of specification

		/**
			Payment GateWay
		*/
		if($_GET[$this->name.'_paymentgateway']){
			$this->js(true)->univ()->frameURL("Payment Gateway configuration",$this->app->url('xepan_commerce_paymentgateway'));
		}

		$isDone = false;
		$action = $this->js()->reload([$this->name.'_paymentgateway'=>1]);
		if($this->add('xepan\commerce\Model_PaymentGateway')->count()->getOne() > 0){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have already added Payment Gateway , visit page ? <a href="'. $this->app->url('xepan_commerce_paymentgateway')->getURL().'"> click here to go </a>');
		}

		$this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Payment Gateway')
			->setMessage('Integrate payment gateway for online paymnet transaction')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		// end of payment gateway

		/**
			Terms & Condition
		*/
		if($_GET[$this->name.'_termandcondition']){
			$this->js(true)->univ()->frameURL("Terms and Condition",$this->app->url('xepan_commerce_tnc'));
		}

		$isDone = false;
		$action = $this->js()->reload([$this->name.'_termandcondition'=>1]);
		if($this->add('xepan\commerce\Model_TNC')->count()->getOne() > 0){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have already added Terms and Condition , visit page ? <a href="'. $this->app->url('xepan_commerce_tnc')->getURL().'"> click here to go </a>');
		}

		$this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Terms & Condition')
			->setMessage('add your company/product terms and condition used for sale order/invoice or purchase order/invoice etc.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		// end of terms & condition

		/*............. Round Amount Standard ...............*/
		if($_GET[$this->name.'_round_amount_standard']){
			$this->js(true)->univ()->frameURL("Amount Standard",$this->app->url('xepan_commerce_amountstandard'));
		}

		$isDone = false;
		$action = $this->js()->reload([$this->name.'_round_amount_standard'=>1]);
		$round_amount_standard = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'round_amount_standard'=>'DropDown'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
		$round_amount_standard->tryLoadAny();
		if($round_amount_standard['round_amount_standard']){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have already updated amount standard, visit page ? <a href="'. $this->app->url('xepan_commerce_amountstandard')->getURL().'"> click here to go </a>');
		}
		$amount_standard_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Specify Amount Standard')
			->setMessage('Please mention amount standard for billing your invoices.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);

		/*............. Products / Item  ...............*/
		if($_GET[$this->name.'_set_item']){
			$this->js(true)->univ()->frameURL("Products",$this->app->url('xepan_commerce_item'));
		}
		$isDone = false;
		$action = $this->js()->reload([$this->name.'_set_item'=>1]);
		if($this->add('xepan\commerce\Model_Item')->count()->getOne() >0){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have already added item, visit page ? <a href="'. $this->app->url('xepan_commerce_item')->getURL().'"> click here to go </a>');
		}

		$product_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Products/Item')
			->setMessage('Please add any product/item i.e. according to your online/offline product of organization.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
		// end of product/item

		// QSP Config Layout
		if($_GET[$this->name.'_set_qspconfig']){
			$this->js(true)->univ()->frameURL("QSP Configuration",$this->app->url('xepan_commerce_qspconfig'));
		}
		$isDone = false;
		$action = $this->js()->reload([$this->name.'_set_qspconfig'=>1]);

		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		if($qsp_config['quotation_serial'] || $qsp_config['sale_order_serial'] || $qsp_config['sale_invoice_serial']){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You have updated QSP Serial Numbers, visit page ? <a href="'. $this->app->url('xepan_commerce_qspconfig')->getURL().'"> click here to go </a>');
		}

		$this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('QSP Config')
			->setMessage('Update prefix string of serial for quotation, sale order and sale invoice.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);


		/*............. Documents Layouts ...............*/
		if($_GET[$this->name.'_documents_layouts']){
			$quotation_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'QUOTATION_LAYOUT',
						'application'=>'commerce'
				]);
			$quotation_m->tryLoadAny();
			
			$quot_master = $quotation_m['master'];
			$quot_detail = $quotation_m['detail'];
			
			$quotation_master_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-master-quotation.html'));
			$quotation_detail_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html'));

			$salesorder_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'from_email'=>'Dropdown',
								'subject'=>'line',
								'body'=>'xepan\base\RichText',
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'SALESORDER_LAYOUT',
						'application'=>'commerce'
				]);
			$salesorder_m->tryLoadAny();
			
			$sales_ord_master = $salesorder_m['master'];
			$sales_ord_detail = $salesorder_m['detail'];

			$salesorder_master_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesorder.html'));
			$salesorder_detail_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html'));

			$salesinvoice_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'from_email'=>'Dropdown',
								'subject'=>'Line',
								'body'=>'xepan\base\RichText',
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'SALESINVOICE_LAYOUT',
						'application'=>'commerce'
				]);
			$salesinvoice_m->tryLoadAny();

			$sales_inv_master = $salesinvoice_m['master'];
			$sales_inv_detail = $salesinvoice_m['detail'];
			
			$salesinvoice_master_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesinvoice.html'));
			$salesinvoice_detail_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html'));

			$purchaseorder_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'PURCHASEORDER_LAYOUT',
						'application'=>'commerce'
				]);
			$purchaseorder_m->tryLoadAny();

			$purchase_ord_master = $purchaseorder_m['master'];
			$purchase_ord_detail = $purchaseorder_m['detail'];
			
			$purchaseorder_master_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseorder.html'));
			$purchaseorder_detail_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html'));

			$purchaseinvoice_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'PURCHASEINVOICE_LAYOUT',
						'application'=>'commerce'
				]);
			$purchaseinvoice_m->tryLoadAny();

			$purchase_inv_master = $purchaseinvoice_m['master'];
			$purchase_inv_detail = $purchaseinvoice_m['detail'];
			
			$purchaseinvoice_master_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseinvoice.html'));
			$purchaseinvoice_detail_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html'));

			$challan_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'CHALLAN_LAYOUT',
					'application'=>'commerce'
			]);
			$challan_m->tryLoadAny();

			$challan_master = $challan_m['master'];
			$challan_detail = $challan_m['detail'];

			$challan_master_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-challan.html'));
			$challan_detail_template = file_get_contents(realpath(getcwd().'/vendor/xepan/commerce/templates/view/print-templates/duplicate-challan-detail.html'));

			if(!$quot_master){
				$quotation_m['master'] = $quotation_master_template;
			}
			if(!$quot_detail){
				$quotation_m['detail'] = $quotation_detail_template;
			}
			$quotation_m->save();

			if(!$sales_ord_master){
				$salesorder_m['master'] = $salesorder_master_template;
			}
			if(!$sales_ord_detail){
				$salesorder_m['detail'] = $salesorder_detail_template;
			}
			$salesorder_m->save();

			if(!$sales_inv_master){
				$salesinvoice_m['master'] = $salesinvoice_master_template;
			}

			if(!$sales_inv_detail){
				$salesinvoice_m['detail'] = $salesinvoice_detail_template;
			}
			$salesinvoice_m->save();

			if(!$purchase_ord_master){
				$purchaseorder_m['master'] = $purchaseorder_master_template;
			}

			if(!$purchase_ord_detail){
				$purchaseorder_m['detail'] = $purchaseorder_detail_template;
			}
			$purchaseorder_m->save();

			if(!$purchase_inv_master){
				$purchaseinvoice_m['master'] = $purchaseinvoice_master_template;
			}

			if(!$purchase_inv_detail){
				$purchaseinvoice_m['detail'] = $purchaseinvoice_detail_template;
			}

			$purchaseinvoice_m->save();

			if(!$challan_master){
				$challan_m['master'] = $challan_master_template;
			}

			if(!$challan_detail){
				$challan_m['detail'] = $challan_detail_template;
			}

			$challan_m->save();

			$this->js(true)->univ()->frameURL("Documents Layouts",$this->app->url('xepan_commerce_layouts'));
		}

		$isDone = false;
		$action = $this->js()->reload([$this->name.'_documents_layouts'=>1]);

			$quotation_m = $this->add('xepan\base\Model_ConfigJsonModel',
					[
						'fields'=>[
									'master'=>'xepan\base\RichText',
									'detail'=>'xepan\base\RichText',
									],
							'config_key'=>'QUOTATION_LAYOUT',
							'application'=>'commerce'
					]);
			$quotation_m->tryLoadAny();
			
			$q_master = $quotation_m['master'];
			$q_detail = $quotation_m['detail'];
			
			$salesorder_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'from_email'=>'Dropdown',
								'subject'=>'line',
								'body'=>'xepan\base\RichText',
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'SALESORDER_LAYOUT',
						'application'=>'commerce'
				]);
			$salesorder_m->tryLoadAny();
			
			$sal_ord_master = $salesorder_m['master'];
			$sal_ord_detail = $salesorder_m['detail'];

			$salesinvoice_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'from_email'=>'Dropdown',
								'subject'=>'Line',
								'body'=>'xepan\base\RichText',
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'SALESINVOICE_LAYOUT',
						'application'=>'commerce'
				]);
			$salesinvoice_m->tryLoadAny();

			$sal_inv_master = $salesinvoice_m['master'];
			$sal_inv_detail = $salesinvoice_m['detail'];
			
			$purchaseorder_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'PURCHASEORDER_LAYOUT',
						'application'=>'commerce'
				]);
			$purchaseorder_m->tryLoadAny();

			$pur_ord_master = $purchaseorder_m['master'];
			$pur_ord_detail = $purchaseorder_m['detail'];
			
			$purchaseinvoice_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'master'=>'xepan\base\RichText',
								'detail'=>'xepan\base\RichText',
								],
						'config_key'=>'PURCHASEINVOICE_LAYOUT',
						'application'=>'commerce'
				]);
			$purchaseinvoice_m->tryLoadAny();

			$pur_inv_master = $purchaseinvoice_m['master'];
			$pur_inv_detail = $purchaseinvoice_m['detail'];

			$challan_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'CHALLAN_LAYOUT',
					'application'=>'commerce'
			]);
			$challan_m->tryLoadAny();

			$chln_master = $challan_m['master'];
			$chln_detail = $challan_m['detail'];
			
			if(!$q_master || !$q_detail || !$sal_ord_master || !$sal_ord_detail || !$sal_inv_master || !$sal_inv_detail || !$pur_ord_master || !$pur_ord_detail || !$pur_inv_master || !$pur_inv_detail || !$chln_master || !$chln_detail){
				$isDone = false;
			}else{
				$isDone = true;
				$action = $this->js()->univ()->dialogOK("Already have Templates",' You have already updated documents layouts for printing, visit page ? <a href="'. $this->app->url('xepan_commerce_layouts')->getURL().'"> click here to go </a>');
			}

		$documents_layouts_view = $this->add('xepan\base\View_Wizard_Step')
			->setAddOn('Application - Commerce')
			->setTitle('Set Documents Layouts For Genrate Pdf For Printing')
			->setMessage('Please set documents layouts for generate pdf for sending to your customer or prints of orders & invoices.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
	}
}