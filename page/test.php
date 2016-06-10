<?php

namespace xepan\commerce;

class page_test extends \xepan\base\Page{
	
	function init(){
		parent::init();

		$categorydetail_tool = $this->add('xepan\commerce\View_CategoryLister');
	}
}