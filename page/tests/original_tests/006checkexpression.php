<?php

namespace xepan\commerce;

class page_tests_006checkexpression extends \xepan\base\Page_Tester{
	public $title = "Expression Tests";

	function init(){
		$this->add('xepan\commerce\page_tests_init');
		parent::init();
	}

    function prepare_totalOrdersCheck(){
        $this->proper_responses['test_totalOrdersCheck']="ToDo";
    }

	function test_totalOrdersCheck(){
        return "ToDo1";    
    }

    function prepare_totalSalesCheck(){
        $this->proper_responses['test_totalSalesCheck']="ToDo";
    }

	function test_totalSalesCheck(){
        return "ToDo1";    
    }

}