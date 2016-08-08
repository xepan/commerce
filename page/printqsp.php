<?php

namespace xepan\commerce;

class page_printqsp extends \Page{

	function init(){
		parent::init();

			if(!$document_id = $_GET['document_id'])
				throw $this->exception('Document Id not found in Query String');

			$document= $this->add('xepan\commerce\Model_QSP_Master')->load($document_id);

			$document= $this->add('xepan\commerce\Model_'.$document['type']);
			$document->load($document_id);
			
			$document->generatePDF('dump');

	}
} 