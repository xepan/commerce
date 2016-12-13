<?php

namespace xepan\commerce;

class page_layouts extends \xepan\commerce\page_configurationsidebar{
	public $title = "Layouts";
	function init(){
		parent::init();
		
		
		/*=========== START QUOTATIONS LAYOUT CONFIG =============================*/

		$quotation_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'QUOTATION_LAYOUT',
					'application'=>'commerce'
			]);
		$quotation_m->add('xepan\hr\Controller_ACL');
		$quotation_m->tryLoadAny();

		$quotation_form = $this->add('Form',null,'quotation');
		$quotation_form->setModel($quotation_m);

		// $quotation_form->getElement('master')->set($quotation_m['master']);
		// $quotation_form->getElement('detail')->set($quotation_m['detail']);
		$save = $quotation_form->addSubmit('Save')->addClass('btn btn-primary');
		$reset = $quotation_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		if($quotation_form->isSubmitted()){
			if($quotation_form->isClicked($save)){
				$quotation_form->save();
				$quotation_m->app->employee
			    ->addActivity("Quotation Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$quotation_m);
				return $quotation_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($quotation_form->isClicked($reset)){
				$qtemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-quotation.html"));
				$qtemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				
				$quotation_m['master'] = $qtemp;
				$quotation_m['detail'] = $qtemp1;
				$quotation_m->save();
				$quotation_m->app->employee
			    ->addActivity("Quotation Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$quotation_m);			
				return $quotation_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		/*=========== End QUOTATIONS LAYOUT CONFIG =============================*/


		/*=========== SRART SALE ORDER LAYOUT CONFIG =============================*/

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
		$salesorder_m->add('xepan\hr\Controller_ACL');
		$salesorder_m->tryLoadAny();

		$sales_order_form = $this->add('Form',null, 'salesorder');
		$sales_order_form->setModel($salesorder_m);
		$sales_order_form->getElement('from_email')->set($salesorder_m['from_email'])->setModel('xepan\communication\Model_Communication_EmailSetting',['name']);
		// $sales_order_form->getElement('subject')->set($salesorder_m['subject']);
		$sales_order_form->getElement('body')->set($salesorder_m['body'])->setFieldHint('{$contact},{$first_name},{$last_name},{$name},{$user},{$emails_str},{$contacts_str},{$organization},{$post},{$address},{$city},{$state},{$pin_code},{$country},{$created_at},{$billing_address},{$billing_pincode},{$billing_city},{$billing_state},{$billing_country},{$shipping_address},{$shipping_city},{$shipping_pincode},{$shipping_state},{$shipping_country},{$search_string},{$document_no},{$related_qsp_master},{$total_amount},{$gross_amount},{$discount_amount},{$net_amount},{$net_amount_self_currency},{$round_amount},{$exchange_rate},{$narration},{$tnc},{$tnc_text}');
		// $sales_order_form->getElement('master')->set($salesorder_m['master']);
		// $sales_order_form->getElement('detail')->set($salesorder_m['detail']);
		
		$so_save = $sales_order_form->addSubmit('Save')->addClass('btn btn-primary');
		$so_reset = $sales_order_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		if($sales_order_form->isSubmitted()){
			if($sales_order_form->isClicked($so_save)){
				$sales_order_form->save();
				$salesorder_m->app->employee
			    ->addActivity("Sales Order Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$salesorder_m);

				return $sales_order_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($sales_order_form->isClicked($so_reset)){
				$sotemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesorder.html"));
				$sotemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				
				$salesorder_m['master']= $sotemp;	
				$salesorder_m['detail']= $sotemp1;	
				$salesorder_m->save();
				$salesorder_m->app->employee
			    ->addActivity("Sales Order Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$salesorder_m);

				return $sales_order_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		/*=========== END SALE ORDER LAYOUT CONFIG =============================*/


		/*=========== START SALE INVOICE LAYOUT CONFIG =============================*/

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
		$salesinvoice_m->add('xepan\hr\Controller_ACL');
		$salesinvoice_m->tryLoadAny();

		$sales_invoice_form = $this->add('Form',null, 'salesinvoice');
		$sales_invoice_form->setModel($salesinvoice_m);
		$sales_invoice_form->getElement('from_email')->set($salesinvoice_m['from_email'])->setModel('xepan\communication\Model_Communication_EmailSetting',['name']);
		// $sales_invoice_form->getElement('subject')->set($salesinvoice_m['subject']);
		$sales_invoice_form->getElement('body')->set($salesinvoice_m['body'])->setFieldHint('{$contact},{$first_name},{$last_name},{$name},{$user},{$emails_str},{$contacts_str},{$organization},{$post},{$address},{$city},{$state},{$pin_code},{$country},{$created_at},{$billing_address},{$billing_pincode},{$billing_city},{$billing_state},{$billing_country},{$shipping_address},{$shipping_city},{$shipping_pincode},{$shipping_state},{$shipping_country},{$search_string},{$document_no},{$related_qsp_master},{$total_amount},{$gross_amount},{$discount_amount},{$net_amount},{$net_amount_self_currency},{$round_amount},{$exchange_rate},{$narration},{$tnc},{$tnc_text},{$customer_pan_no},{$customer_tin_no},{$order_no},{$order_date},{$total_amount}');
		// $sales_invoice_form->getElement('master')->set($salesinvoice_m['master']);
		// $sales_invoice_form->getElement('detail')->set($salesinvoice_m['detail']);
		
		$si_save = $sales_invoice_form->addSubmit('Save')->addClass('btn btn-primary');
		$si_reset = $sales_invoice_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		if($sales_invoice_form->isSubmitted()){
			if($sales_invoice_form->isClicked($si_save)){
				$sales_invoice_form->save();
				$salesinvoice_m->app->employee
			    ->addActivity("Sales Invoice Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$salesinvoice_m);

				return $sales_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($sales_invoice_form->isClicked($si_reset)){
				$sitemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesinvoice.html"));
				// $this->app->epan->config->setConfig('SALESINVOICELAYOUT',$sitemp,'commerce');
				$sitemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				// $this->app->epan->config->setConfig('SALESINVOICEDETAILLAYOUT',$sitemp1,'commerce');
				$salesinvoice_m['master']= $sitemp;	
				$salesinvoice_m['detail']= $sitemp1;	
				$salesinvoice_m->save();
				$salesinvoice_m->app->employee
			    ->addActivity("Sales Invoice Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$salesinvoice_m);
				
				return $sales_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		/*=========== END SALE INVOICE LAYOUT CONFIG =============================*/

		/*=========== START PURCHASE ORDER LAYOUT CONFIG =============================*/

		$purchaseorder_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'PURCHASEORDER_LAYOUT',
					'application'=>'commerce'
			]);
		$purchaseorder_m->add('xepan\hr\Controller_ACL');
		$purchaseorder_m->tryLoadAny();


		$purchase_order_form = $this->add('Form',null, 'purchaseorder');
		$purchase_order_form->setModel($purchaseorder_m);
		// $purchase_order_form->getElement('master')->set($purchaseorder_m['master']);
		// $purchase_order_form->getElement('detail')->set($purchaseorder_m['detail']);
		$po_save = $purchase_order_form->addSubmit('Save')->addClass('btn btn-primary');
		$po_reset = $purchase_order_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		if($purchase_order_form->isSubmitted()){
			if($purchase_order_form->isClicked($po_save)){
				$purchase_order_form->save();
				$purchaseorder_m->app->employee
			    ->addActivity("Purchase Order Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$purchaseorder_m);

				return $purchase_order_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($purchase_order_form->isClicked($po_reset)){
				$potemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseorder.html"));
				$potemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));

				$purchaseorder_m['master']= $potemp;	
				$purchaseorder_m['detail']= $potemp1;	
				$purchaseorder_m->save();
				$purchaseorder_m->app->employee
			    ->addActivity("Purchase Order Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$purchaseorder_m);

				return $purchase_order_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		/*=========== END PURCHASE ORDER LAYOUT CONFIG =============================*/


		/*=========== START PURCHASE INVOICE LAYOUT CONFIG =============================*/

		$purchaseinvoice_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'PURCHASEINVOICE_LAYOUT',
					'application'=>'commerce'
			]);
		$purchaseinvoice_m->add('xepan\hr\Controller_ACL');
		$purchaseinvoice_m->tryLoadAny();


		$purchase_invoice_form = $this->add('Form',null, 'purchaseinvoice');
		$purchase_invoice_form->setModel($purchaseinvoice_m);
		// $purchase_invoice_form->getElement('master')->set($purchaseinvoice_m['master']);
		// $purchase_invoice_form->getElement('detail')->set($purchaseinvoice_m['detail']);
		$pi_save = $purchase_invoice_form->addSubmit('Save')->addClass('btn btn-primary');
		$pi_reset = $purchase_invoice_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		if($purchase_invoice_form->isSubmitted()){
			if($purchase_invoice_form->isClicked($pi_save)){
				$purchase_invoice_form->save();
				$purchaseinvoice_m->app->employee
			    ->addActivity("Purchase Invoice Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$purchaseinvoice_m);
				return $purchase_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($purchase_invoice_form->isClicked($pi_reset)){
				$pitemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseinvoice.html"));
				$pitemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				$purchaseinvoice_m['master']= $pitemp;	
				$purchaseinvoice_m['detail']= $pitemp1;	
				$purchaseinvoice_m->save();
				$purchaseinvoice_m->app->employee
			    ->addActivity("Purchase Invoice Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$purchaseinvoice_m);
				
				return $purchase_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		/*=========== END PURCHASE INVOICE LAYOUT CONFIG =============================*/


		/*=========== START  CHALAN LAYOUT CONFIG =============================*/
		$challan_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'master'=>'xepan\base\RichText',
							'detail'=>'xepan\base\RichText',
							],
					'config_key'=>'CHALLAN_LAYOUT',
					'application'=>'commerce'
			]);
		$challan_m->add('xepan\hr\Controller_ACL');
		$challan_m->tryLoadAny();

		$challan_form = $this->add('Form',null, 'challan');
		$challan_form->setModel($challan_m);
		// $challan_form->getElement('master')->set($challan_m['master']);
		// $challan_form->getElement('detail')->set($challan_m['detail']);
		$c_save = $challan_form->addSubmit('Save')->addClass('btn btn-primary');
		$c_reset = $challan_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		if($challan_form->isSubmitted()){
			if($challan_form->isClicked($c_save)){
				$challan_form->save();
				$challan_m->app->employee
			    ->addActivity("Challan Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$challan_m);
				return $challan_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($challan_form->isClicked($c_reset)){
				$ctemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-challan.html"));
				$ctemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-challan-detail.html"));
				$challan_m['master']= $ctemp;	
				$challan_m['detail']= $ctemp1;	
				$challan_m->save();
				$challan_m->app->employee
			    ->addActivity("Challan Printing Layout Updated", null/* Related Document ID*/, null /*Related Contact ID*/,null,null,"xepan_commerce_layouts")
				->notifyWhoCan(' ',' ',$challan_m);
				return $challan_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}


	}

	function defaultTemplate(){
		return['page\layout'];
	}
}