<?php
 namespace xepan\commerce;
 class page_viewitem extends \Page{

 	public $title='View Item';


	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return['page/viewitem'];
	}
}