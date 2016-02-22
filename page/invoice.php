<?php
 namespace xepan\commerce;
 class page_invoice extends \Page{

 	public $title='Order invoice';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/invoice'];
	}
}