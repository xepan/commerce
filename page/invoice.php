<?php
 namespace xepan\commerce;
 class page_invoice extends \Page{

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/invoice'];
	}
}