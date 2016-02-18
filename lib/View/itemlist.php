<?php
 namespace xepan\commerce;
 class View_itemlist extends \View{

 	function init(){
		parent::init();
 		//$this->add('CRUD')->setModel('xepan\commerce\Itemlist');
 	}


 function defaultTemplate(){

 	return ['view/itemlist'];
 	 }

 }