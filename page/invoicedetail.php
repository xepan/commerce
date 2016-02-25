<?php
 namespace xepan\commerce;
 class page_invoicedetail extends \Page{

 	public $title='Sales Invoice Detail';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/invoicedetail'];
	}
}