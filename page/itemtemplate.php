<?php  

/**
* description: ATK Page
* 
* @author : Rakesh Sinha
* @email : rksinha.btech@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/ 

 namespace xepan\commerce;

 class page_itemtemplate extends \Page {
	public $title='Item Template';

	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		

	}

	function defaultTemplate(){
		return ['page/item/template'];

	}
}


