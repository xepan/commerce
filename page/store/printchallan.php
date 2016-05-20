<?php

namespace xepan\commerce;

class page_store_printchallan extends \Page{

	function init(){
		parent::init();

			if(!$transaction_id = $_GET['transaction_id'])
				throw $this->exception('Document Id not found in Query String');

			$transaction = $this->add('xepan\commerce\Model_Store_Delivered')->load($transaction_id);
			$transaction->generatePDF('dump');

	}
} 