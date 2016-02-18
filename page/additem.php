<?php
 namespace xepan\commerce;
 class page_additem extends \Page{
 	public $title='AddItem';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/additem'];
	}
}