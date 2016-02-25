<?php
 namespace xepan\commerce;
 class page_purchaseinvoicedetail extends \Page{

 	public $title='Purchase Invoice Detail';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/purchaseinvoicedetail'];
	}
}