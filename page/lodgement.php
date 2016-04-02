<?php

namespace xepan\commerce;

class page_lodgement extends \Page{

	function init(){
		parent::init();


		$crud = $this->add('CRUD');
		$invoice = $this->add('xepan\commerce\Model_InvoiceTransactionAssociation');

		$crud->setModel($invoice);


	}
} 