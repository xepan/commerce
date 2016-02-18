<?php
 namespace xepan\commerce;
 class page_purchaseorder extends \Page{

 	public $title='Purchase;


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/purchaseorder'];
	}
}