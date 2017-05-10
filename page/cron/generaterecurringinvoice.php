<?php
 
namespace xepan\commerce;

class page_cron_generaterecurringinvoice extends \xepan\base\Page {

	public $title='Recurring Invoice Cron Page';

	function init(){
		parent::init();
			
		// $this->add('CRUD')->setModel('xepan\commerce\RecurringInvoiceItem',['is_recurring','renewable_value','renewable_unit','created_at','invoice_recurring_date']);
		// recurringinvoice
		$this->add('xepan\commerce\Controller_GenerateRecurringInvoice')->run();
	}
}
