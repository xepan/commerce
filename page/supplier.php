<?php
 namespace xepan\commerce;
 class page_supplier extends \Page{

 	public $title='Supplier';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/supplier'];
	}
}