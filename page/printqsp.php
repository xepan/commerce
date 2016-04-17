<?php

namespace xepan\commerce;

class page_printqsp extends \Page{

	function init(){
		parent::init();

			$print_invoice_array=[];
			if($_GET['printAll']){
				$print_invoice=$this->add('xepan/commerce/Model_QSP_Master');

				// if($_GET['from_date'])
    //             	$print_order->addCondition('created_at','>=',$_GET['from_date']);
    //         	if($_GET['to_date'])
    //             	$print_order->addCondition('created_at','<=',$_GET['to_date']);
            	if($_GET['contact_id'])
                	$print_order->addCondition('contact_id',$_GET['contact_id']);
            	// if($_GET['order_id'])
             //    	$print_order->addCondition('order_id',$_GET['order_id']);
            	if($_GET['status'])
                	$print_order->addCondition('status',$_GET['status']);

	            $all_order = $print_invoice->_dsql()->del('fields')->field('id')->getAll();
	           	$print_invoice_array = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($all_order)),false);
				
			}else{
				$print_invoice_array[] = $invoice_id = $this->api->StickyGET('document_id');

		}

		$css=array(
			'templates/css/noprint.css',
		);
		
		foreach ($print_invoice_array as $key => $invoice_id){
			//var_dump($print_invoice_array);

				if(!$invoice_id){
					$this->add('View_Warning')->set('QSP Not Found');
					return;
				}

				$invoice=$this->add('xepan\commerce\Model_QSP_Master')->tryload($invoice_id);

				foreach ($css as $css_file) {
				$link = $this->add('View')->setElement('link');
				$link->setAttr('rel',"stylesheet");
				$link->setAttr('type',"text/css");
				$link->setAttr('href',$css_file);
				$link->setAttr('media','print');
				}

				
				echo $link->getHtml();
				echo $invoice->parseEmailBody();
		}

		exit;


	}
} 