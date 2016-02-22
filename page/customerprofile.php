<?php
 namespace xepan\commerce;
 class page_customerprofile extends \Page{
 	public $title='Customer';

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/customerprofile'];
	}
}