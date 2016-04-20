<?php

namespace xepan\commerce;

class page_tests_004checkstock extends \xepan\base\Page_Tester{
	public $title = "Stock Tests";

	function init(){
		$this->add('xepan\commerce\page_tests_init');
		parent::init();
	}

    function prepare_stockCheck(){
        $this->proper_responses['test_stockCheck']="ToDo";
    }

	function test_stockCheck(){
        return "ToDo1";    
    }

}