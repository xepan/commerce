<?php

namespace xepan\commerce;

class page_layouts extends \xepan\commerce\page_configurationsidebar{
	public $title = "Layouts";
	function init(){
		parent::init();
		/*
			GETTING VALUES FROM CONFIG
		*/
		
		$q_layout = $this->app->epan->config->getConfig('QUOTATIONLAYOUT');
		$so_layout = $this->app->epan->config->getConfig('SALESORDERLAYOUT');
		$si_layout = $this->app->epan->config->getConfig('SALESINVOICELAYOUT');
		$po_layout = $this->app->epan->config->getConfig('PURCHASEORDERLAYOUT');
		$pi_layout = $this->app->epan->config->getConfig('PURCHASEINVOICELAYOUT');
		$c_layout = $this->app->epan->config->getConfig('CHALLANLAYOUT');

		$dq_layout = $this->app->epan->config->getConfig('QUOTATIONDETAILLAYOUT');
		$dso_layout = $this->app->epan->config->getConfig('SALESORDERDETAILLAYOUT');
		$dsi_layout = $this->app->epan->config->getConfig('SALESINVOICEDETAILLAYOUT');
		$dpo_layout = $this->app->epan->config->getConfig('PURCHASEORDERDETAILLAYOUT');
		$dpi_layout = $this->app->epan->config->getConfig('PURCHASEINVOICEDETAILLAYOUT');
		

		/*
			DECLARING FORM FOR SETTING LAYOUTS
		*/ 
		
		$quotation_form = $this->add('Form',null,'quotation');
		$quotation_form->addField('xepan\base\RichText','quotation_layout')->set($q_layout);
		$quotation_form->addField('xepan\base\RichText','quotation_detail_layout')->set($dq_layout);
		$save = $quotation_form->addSubmit('Save')->addClass('btn btn-primary');
		$reset = $quotation_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$sales_order_form = $this->add('Form',null, 'salesorder');
		$sales_order_config = $this->app->epan->config;
		$salesorder_subject= $sales_order_config->getConfig('SALES_ORDER_SUBJECT_ONLINE');
		$salesorder_body= $sales_order_config->getConfig('SALES_ORDER_BODY_ONLINE');
		$salesorder_from_email= $sales_order_config->getConfig('SALES_ORDER_FROM_EMAIL_ONLINE');

		$sales_order_form->addField('Dropdown','from_email')->set($salesorder_from_email)->setModel('xepan\communication\Model_Communication_EmailSetting',['name']);
		$sales_order_form->addField('line','subject')->set($salesorder_subject);
		$sales_order_form->addField('xepan\base\RichText','body')->set($salesorder_body)->setFieldHint('{$contact},{$first_name},{$last_name},{$name},{$user},{$emails_str},{$contacts_str},{$organization},{$post},{$address},{$city},{$state},{$pin_code},{$country},{$created_at},{$billing_address},{$billing_pincode},{$billing_city},{$billing_state},{$billing_country},{$shipping_address},{$shipping_city},{$shipping_pincode},{$shipping_state},{$shipping_country},{$search_string},{$document_no},{$related_qsp_master},{$total_amount},{$gross_amount},{$discount_amount},{$net_amount},{$net_amount_self_currency},{$round_amount},{$exchange_rate},{$narration},{$tnc},{$tnc_text}');


		$sales_order_form->addField('xepan\base\RichText','sales_order_layout')->set($so_layout);
		$sales_order_form->addField('xepan\base\RichText','sales_order_detail_layout')->set($dso_layout);
		$so_save = $sales_order_form->addSubmit('Save')->addClass('btn btn-primary');
		$so_reset = $sales_order_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$sales_invoice_form = $this->add('Form',null, 'salesinvoice');
		$sales_invoice_config = $this->app->epan->config;
		$salesinvoice_subject= $sales_invoice_config->getConfig('SALES_INVOICE_SUBJECT_ONLINE');
		$salesinvoice_body= $sales_invoice_config->getConfig('SALES_INVOICE_BODY_ONLINE');
		$salesinvoice_from_email= $sales_invoice_config->getConfig('SALES_INVOICE_FROM_EMAIL_ONLINE');

		$sales_invoice_form->addField('Dropdown','from_email')->set($salesinvoice_from_email)->setModel('xepan\communication\Model_Communication_EmailSetting',['name']);
		$sales_invoice_form->addField('line','subject')->set($salesinvoice_subject);
		$sales_invoice_form->addField('xepan\base\RichText','body')->set($salesinvoice_body)->setFieldHint('{$contact},{$first_name},{$last_name},{$name},{$user},{$emails_str},{$contacts_str},{$organization},{$post},{$address},{$city},{$state},{$pin_code},{$country},{$created_at},{$billing_address},{$billing_pincode},{$billing_city},{$billing_state},{$billing_country},{$shipping_address},{$shipping_city},{$shipping_pincode},{$shipping_state},{$shipping_country},{$search_string},{$document_no},{$related_qsp_master},{$total_amount},{$gross_amount},{$discount_amount},{$net_amount},{$net_amount_self_currency},{$round_amount},{$exchange_rate},{$narration},{$tnc},{$tnc_text},{$customer_pan_no},{$customer_tin_no},{$order_no},{$order_date}');

		$sales_invoice_form->addField('xepan\base\RichText','sales_invoice_layout')->set($si_layout);
		$sales_invoice_form->addField('xepan\base\RichText','sales_invoice_detail_layout')->set($dsi_layout);
		$si_save = $sales_invoice_form->addSubmit('Save')->addClass('btn btn-primary');
		$si_reset = $sales_invoice_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$purchase_order_form = $this->add('Form',null, 'purchaseorder');
		$purchase_order_form->addField('xepan\base\RichText','purchase_order_layout')->set($po_layout);
		$purchase_order_form->addField('xepan\base\RichText','purchase_order_detail_layout')->set($dpo_layout);
		$po_save = $purchase_order_form->addSubmit('Save')->addClass('btn btn-primary');
		$po_reset = $purchase_order_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$purchase_invoice_form = $this->add('Form',null, 'purchaseinvoice');
		$purchase_invoice_form->addField('xepan\base\RichText','purchase_invoice_layout')->set($pi_layout);
		$purchase_invoice_form->addField('xepan\base\RichText','purchase_invoice_detail_layout')->set($dpi_layout);
		$pi_save = $purchase_invoice_form->addSubmit('Save')->addClass('btn btn-primary');
		$pi_reset = $purchase_invoice_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$challan_form = $this->add('Form',null, 'challan');
		$challan_form->addField('xepan\base\RichText','challan_layout')->set($c_layout);
		$c_save = $challan_form->addSubmit('Save')->addClass('btn btn-primary');
		$c_reset = $challan_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		/*
			HANDLING FORM SUBMISSIONS
		*/

		if($quotation_form->isSubmitted()){
			if($quotation_form->isClicked($save)){
				$this->app->epan->config->setConfig('QUOTATIONLAYOUT',$quotation_form['quotation_layout'],'commerce');
				$this->app->epan->config->setConfig('QUOTATIONDETAILLAYOUT',$quotation_form['quotation_detail_layout'],'commerce');
				return $quotation_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($quotation_form->isClicked($reset)){
				$qtemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-quotation.html"));
				$this->app->epan->config->setConfig('QUOTATIONLAYOUT',$qtemp,'commerce');

				$qtemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				$this->app->epan->config->setConfig('QUOTATIONDETAILLAYOUT',$qtemp1,'commerce');
				
				return $quotation_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($sales_order_form->isSubmitted()){
			if($sales_order_form->isClicked($so_save)){
				$sales_order_config->setConfig('SALES_ORDER_FROM_EMAIL_ONLINE',$sales_order_form['from_email'],'commerce');
				$sales_order_config->setConfig('SALES_ORDER_SUBJECT_ONLINE',$sales_order_form['subject'],'commerce');
				$sales_order_config->setConfig('SALES_ORDER_BODY_ONLINE',$sales_order_form['body'],'commerce');

				$this->app->epan->config->setConfig('SALESORDERLAYOUT',$sales_order_form['sales_order_layout'],'commerce');
				$this->app->epan->config->setConfig('SALESORDERDETAILLAYOUT',$sales_order_form['sales_order_detail_layout'],'commerce');
				
				return $sales_order_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($sales_order_form->isClicked($so_reset)){
				$sotemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesorder.html"));
				$this->app->epan->config->setConfig('SALESORDERLAYOUT',$sotemp,'commerce');

				$sotemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				$this->app->epan->config->setConfig('SALESORDERDETAILLAYOUT',$sotemp1,'commerce');
				
				return $sales_order_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($sales_invoice_form->isSubmitted()){
			if($sales_invoice_form->isClicked($si_save)){
				$sales_invoice_config->setConfig('SALES_INVOICE_FROM_EMAIL_ONLINE',$sales_invoice_form['from_email'],'commerce');
				$sales_invoice_config->setConfig('SALES_INVOICE_SUBJECT_ONLINE',$sales_invoice_form['subject'],'commerce');
				$sales_invoice_config->setConfig('SALES_INVOICE_BODY_ONLINE',$sales_order_form['body'],'commerce');
				
				$this->app->epan->config->setConfig('SALESINVOICELAYOUT',$sales_invoice_form['sales_invoice_layout'],'commerce');
				$this->app->epan->config->setConfig('SALESINVOICEDETAILLAYOUT',$sales_invoice_form['sales_invoice_detail_layout'],'commerce');
				
				return $sales_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($sales_invoice_form->isClicked($si_reset)){
				$sitemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesinvoice.html"));
				$this->app->epan->config->setConfig('SALESINVOICELAYOUT',$sitemp,'commerce');

				$sitemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				$this->app->epan->config->setConfig('SALESINVOICEDETAILLAYOUT',$sitemp1,'commerce');
				
				return $sales_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($purchase_order_form->isSubmitted()){
			if($purchase_order_form->isClicked($po_save)){
				$this->app->epan->config->setConfig('PURCHASEORDERLAYOUT',$purchase_order_form['purchase_order_layout'],'commerce');
				$this->app->epan->config->setConfig('PURCHASEORDERDETAILLAYOUT',$purchase_order_form['purchase_order_detail_layout'],'commerce');
				
				return $purchase_order_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($purchase_order_form->isClicked($po_reset)){
				$potemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseorder.html"));
				$this->app->epan->config->setConfig('PURCHASEORDERLAYOUT',$potemp,'commerce');

				$potemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				$this->app->epan->config->setConfig('PURCHASEORDERDETAILLAYOUT',$potemp1,'commerce');
				
				return $purchase_order_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($purchase_invoice_form->isSubmitted()){
			if($purchase_invoice_form->isClicked($pi_save)){
				$this->app->epan->config->setConfig('PURCHASEINVOICELAYOUT',$purchase_invoice_form['purchase_invoice_layout'],'commerce');
				$this->app->epan->config->setConfig('PURCHASEINVOICEDETAILLAYOUT',$purchase_invoice_form['purchase_invoice_detail_layout'],'commerce');
				
				return $purchase_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($purchase_invoice_form->isClicked($pi_reset)){
				$pitemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseinvoice.html"));
				$this->app->epan->config->setConfig('PURCHASEINVOICELAYOUT',$pitemp,'commerce');

				$pitemp1 = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"));
				$this->app->epan->config->setConfig('PURCHASEINVOICEDETAILLAYOUT',$pitemp1,'commerce');
				
				return $purchase_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($challan_form->isSubmitted()){
			if($challan_form->isClicked($c_save)){
				$this->app->epan->config->setConfig('CHALLANLAYOUT',$challan_form['challan_layout'],'commerce');
				
				return $challan_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($challan_form->isClicked($c_reset)){
				$ctemp = file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-challan.html"));
				$this->app->epan->config->setConfig('CHALLANLAYOUT',$ctemp,'commerce');

				return $challan_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}			

	}

	function defaultTemplate(){
		return['page\layout'];
	}
}