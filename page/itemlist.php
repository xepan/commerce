<?php
 namespace xepan\commerce;
 class page_itemlist extends \Page{

 	public $title='Items';


 	function init(){
 		parent::init();
		
 	}

 	function defaultTemplate(){

 		return['page/itemlist'];

 	}
 } 