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
		$this->getElement('for')->hint('Document Type like Quotation/SalesOrder/SalesInvoice/PurhcaseOrder etc');
		
	}

}