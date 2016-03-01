<?php
 namespace xepan\commerce;
 class page_additem extends \Page{

 	public $title='Add Item';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/additem'];
	}
}