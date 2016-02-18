<?php
 namespace xepan\commerce;
 class page_invoicepurchase extends \Page{

 	public $title='Purchase Order Invoice';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/invoicepurchase'];
	}
}