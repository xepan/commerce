<?php

namespace xepan\commerce;


class page_setupMidWay extends \xepan\base\Page {
	
	public $title = "Setup in between Mid Session/Year";

	function init(){
		parent::init();

		$form = $this->add('Form');
		$form->addField('DatePicker','year_start_date');
		$form->addSubmit('Execute');


		if($form->isSubmitted()){
			// due and paid invoice transaction created

			$invoices = $this->add('xepan\commerce\Model_SalesInvoice');
			$invoices->addCondition('status',['Due','Paid']);
			$invoices->addCondition('created_at','>=',$form['year_start_date']);

			foreach ($invoices as $inv) {
				$inv->updateTransaction();
			}
		}

	}
}