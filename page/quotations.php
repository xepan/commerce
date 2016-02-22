<?php
 namespace xepan\commerce;
 class page_quotations extends \Page{
 	public $title='Quotaion List';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/quotations'];
	}
}