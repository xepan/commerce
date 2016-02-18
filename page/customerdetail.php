<?php
 namespace xepan\commerce;
 class page_customerdetail extends \Page{

 	public $title='Customer Detail';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/customerdetail'];
	}
}