<?php
 namespace xepan\commerce;
class View_invoice extends View{

	function init(){
		parent::init();
	}

	function defaultTemplate(){

		return ['view/invoice'];
	}

}