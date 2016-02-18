<?php
 namespace xepan\commerce;
 class page_salesorder extends \Page{

 	public $title='Sales Order';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/salesorder'];
	}
}