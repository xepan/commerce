<?php

namespace xepan\commerce;

class page_layouts extends \xepan\commerce\page_configurationsidebar{
	public $title = "Layouts";
	function init(){
		parent::init();
		
		/*
			DECLARING FORM FOR SETTING LAYOUTS
		*/ 

		$quotation_form = $this->add('Form',null,'quotation');
		$quotation_form->addField('xepan\base\RichText','quotation_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-quotation.html")));
		$quotation_form->addField('xepan\base\RichText','quotation_detail_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html")));
		$save = $quotation_form->addSubmit('Save')->addClass('btn btn-primary');
		$reset = $quotation_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$sales_order_form = $this->add('Form',null, 'salesorder');
		$sales_order_form->addField('xepan\base\RichText','sales_order_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-salesorder.html")));
		$sales_order_form->addField('xepan\base\RichText','sales_order_detail_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html")));
		$so_save = $sales_order_form->addSubmit('Save')->addClass('btn btn-primary');
		$so_reset = $sales_order_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$sales_invoice_form = $this->add('Form',null, 'salesinvoice');
		$sales_invoice_config = $this->app->epan->config;
		$salesinvoice_subject= $sales_invoice_config->getConfig('SALES_INVOICE_SUBJECT_ONLINE');
		$salesinvoice_body= $sales_invoice_config->getConfig('SALES_INVOICE_BODY_ONLINE');

		$sales_invoice_form->addField('line','subject')->set($salesinvoice_subject);
		$sales_invoice_form->addField('xepan\base\RichText','body')->set($salesinvoice_body);

		$sales_invoice_form->addField('xepan\base\RichText','sales_invoice_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-salesinvoice.html")));
		$sales_invoice_form->addField('xepan\base\RichText','sales_invoice_detail_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html")));
		$si_save = $sales_invoice_form->addSubmit('Save')->addClass('btn btn-primary');
		$si_reset = $sales_invoice_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$purchase_order_form = $this->add('Form',null, 'purchaseorder');
		$purchase_order_form->addField('xepan\base\RichText','purchase_order_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-purchaseorder.html")));
		$purchase_order_form->addField('xepan\base\RichText','purchase_order_detail_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html")));
		$po_save = $purchase_order_form->addSubmit('Save')->addClass('btn btn-primary');
		$po_reset = $purchase_order_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$purchase_invoice_form = $this->add('Form',null, 'purchaseinvoice');
		$purchase_invoice_form->addField('xepan\base\RichText','purchase_invoice_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-purchaseinvoice.html")));
		$purchase_invoice_form->addField('xepan\base\RichText','purchase_invoice_detail_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html")));
		$pi_save = $purchase_invoice_form->addSubmit('Save')->addClass('btn btn-primary');
		$pi_reset = $purchase_invoice_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		$challan_form = $this->add('Form',null, 'challan');
		$challan_form->addField('xepan\base\RichText','challan_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-challan.html")));
		// $challan_form->addField('xepan\base\RichText','challan_detail_layout')->set(file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/challan.html")));
		$c_save = $challan_form->addSubmit('Save')->addClass('btn btn-primary');
		$c_reset = $challan_form->addSubmit('Reset Default')->addClass('btn btn-primary');

		/*
			HANDLING FORM SUBMISSIONS
		*/

		if($quotation_form->isSubmitted()){
			if($quotation_form->isClicked($save)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-quotation.html"),$quotation_form['quotation_layout']);
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),$quotation_form['quotation_detail_layout']);
				return $quotation_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($quotation_form->isClicked($reset)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-quotation.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-quotation.html"))	
				);

				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"))
				);
				return $quotation_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($sales_order_form->isSubmitted()){
			if($sales_order_form->isClicked($so_save)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-salesorder.html"),$sales_order_form['sales_order_layout']);
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),$sales_order_form['sales_order_detail_layout']);
				return $sales_order_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($sales_order_form->isClicked($so_reset)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-salesorder.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesorder.html"))	
				);

				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"))
				);
				return $sales_order_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($sales_invoice_form->isSubmitted()){
			if($sales_invoice_form->isClicked($si_save)){
				$sales_invoice_config->setConfig('SALES_INVOICE_SUBJECT_ONLINE',$sales_invoice_form['subject'],'commerce');
				$sales_invoice_config->setConfig('SALES_INVOICE_BODY_ONLINE',$sales_invoice_form['body'],'commerce');
				
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-salesinvoice.html"),$sales_invoice_form['sales_invoice_layout']);
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),$sales_invoice_form['sales_invoice_detail_layout']);
				return $sales_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($sales_invoice_form->isClicked($si_reset)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-salesinvoice.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-salesinvoice.html"))	
				);

				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"))
				);
				return $sales_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($purchase_order_form->isSubmitted()){
			if($purchase_order_form->isClicked($po_save)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-purchaseorder.html"),$purchase_order_form['purchase_order_layout']);
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),$purchase_order_form['purchase_order_detail_layout']);
				return $purchase_order_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($purchase_order_form->isClicked($po_reset)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-purchaseorder.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseorder.html"))	
				);

				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"))
				);
				return $purchase_order_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($purchase_invoice_form->isSubmitted()){
			if($purchase_invoice_form->isClicked($pi_save)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-purchaseinvoice.html"),$purchase_invoice_form['purchase_invoice_layout']);
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),$purchase_invoice_form['purchase_invoice_detail_layout']);
				return $purchase_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($purchase_invoice_form->isClicked($pi_reset)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-purchaseinvoice.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-purchaseinvoice.html"))	
				);

				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/print-detail.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-print-detail.html"))
				);
				return $purchase_invoice_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}

		if($challan_form->isSubmitted()){
			if($challan_form->isClicked($c_save)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-challan.html"),$challan_form['challan_layout']);
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/challan.html"),$challan_form['challan_detail_layout']);
				return $challan_form->js()->univ()->successMessage('Saved')->execute();
			}

			if($challan_form->isClicked($c_reset)){
				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/master-challan.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-master-challan.html"))	
				);

				file_put_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/challan.html"),
				file_get_contents(realpath("../vendor/xepan/commerce/templates/view/print-templates/duplicate-challan.html"))
				);
				return $challan_form->js()->univ()->successMessage('Saved')->execute();
			}	
		}			

	}

	function defaultTemplate(){
		return['page\layout'];
	}
}