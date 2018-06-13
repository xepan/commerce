<?php

namespace xepan\commerce;

class Model_Config_QSPActionEmailAndSms extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'for'=>'DropDown',
						'status'=>'line',
						'sms_content'=>'Text',
						'email_subject'=>'line',
						'email_body'=>'Text'
					];
	public $config_key = 'QSP_Action_Email_And_Sms';
	public $application='commerce';

	function init(){
		parent::init();

		$for_field = $this->getElement('for');
		$for_field->hint('Document Type like Quotation/SalesOrder/SalesInvoice/PurhcaseOrder etc')
			->display(['form'=>'DropDown'])
			->enum(['Quotation','SalesOrder','SalesInvoice','PurhcaseOrder','PurchaseInvoice']);

		$this->add('Controller_Validator');
		
		$this->is(['for|required']);
	}

}