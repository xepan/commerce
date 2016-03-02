<?php 
 namespace xepan\commerce;
 class page_salesinvoice extends \Page{

	public $title='SalesInvoice';

	function init(){
		parent::init();

		$sinvoice=$this->add('xepan\commerce\Model_Invoice_SalesInvoice');

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_invoicedetail'],
						null,
						['view/invoice/sale/grid']);

		$crud->setModel($sinvoice);
		$crud->grid->addQuickSearch(['name']);
	}

}  




























// <?php
//  namespace xepan\commerce;
//  class page_salesinvoice extends \Page{

//  	public $title='Sales Invoice';


// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/salesinvoice'];
// 	}
// }