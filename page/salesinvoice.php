<?php
 namespace xepan\commerce;
 class page_salesinvoice extends \Page{

 	public $title='Sales Invoice';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/salesinvoice'];
	}
}