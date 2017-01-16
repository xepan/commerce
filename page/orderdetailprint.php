<?php

namespace xepan\commerce;

class page_orderdetailprint extends \Page{
	function init(){
		parent::init();

		if(!$this->app->auth->isLoggedIn())
			throw new \Exception("Authorization Failed");
		
		$customer = $this->add('xepan\commerce\Model_Customer');
		$customer->loadLoggedIn("Customer");

        $document_id = $this->app->stickyGET('document_id');
        
        if(!$document_id)
            throw $this->exception('Document Id not found');

        $this->app->muteACL = true;
        $document= $this->add('xepan\commerce\Model_QSP_Master')->load($document_id);
		
		if($customer->id != $document['contact_id'])
			throw new \Exception("Authorization Failed");
			
        $pdfname=$this->app->current_website_name.'_order_'.$document['document_no'].'.pdf';
        $data = $document->generatePDF('dump');
        exit;
	}
}