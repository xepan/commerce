<?php 
 namespace xepan\commerce;
 class page_test extends \Page{

	public $title='Test';

	function init(){
		parent::init();

		$this->add('xepan\commerce\Tool_ItemList');
	}

}  
