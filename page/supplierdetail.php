<?php
 namespace xepan\commerce;
 class page_supplierdetail extends \Page{

 	public $title='Supplier Detail';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/supplierdetail'];
	}
}