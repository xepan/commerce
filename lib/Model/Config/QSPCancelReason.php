<?php

namespace xepan\commerce;

class Model_Config_QSPCancelReason extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'for'=>'Line',
						'name'=>"Text"
					];
	public $config_key = 'QSP_Cancel_Reason';
	public $application='commerce';

	function init(){
		parent::init();

		$this->getElement('name')->hint('Comma Seperated Values of Cancel Reason')->caption('values');
		$for_field = $this->getElement('for');
		$for_field->hint('Document Type like Quotation/SalesOrder/SalesInvoice/PurhcaseOrder etc')
			->display(['form'=>'DropDown'])
			->enum(['Quotation','SalesOrder','SalesInvoice','PurhcaseOrder','PurchaseInvoice']);

		$this->add('Controller_Validator');
		
		$this->is(['for|required']);
	}

}