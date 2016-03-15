<?php 
 namespace xepan\commerce;
 class page_test2 extends \Page{

	public $title='Test';

	function init(){
		parent::init();

		$this->add('xepan\commerce\Tool_ItemList');
	}

}  
