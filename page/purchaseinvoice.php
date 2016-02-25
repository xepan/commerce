<?php
 namespace xepan\commerce;
 class page_purchaseinvoice extends \Page{

 	public $title='Purchase Invoice';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/purchaseinvoice'];
	}
}