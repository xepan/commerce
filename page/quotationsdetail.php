<?php
 namespace xepan\commerce;
 class page_quotationsdetail extends \Page{
 	public $title='Quotation Detail';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/quotationsdetail'];
	}
}