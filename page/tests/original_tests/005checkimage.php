<?php

namespace xepan\commerce;

class page_tests_005checkimage extends \xepan\base\Page_Tester{
	public $title = "Image Tests";

	function init(){
		$this->add('xepan\commerce\page_tests_init');
		parent::init();
	}

    function prepare_imageCheck(){
        $this->proper_responses['test_imageCheck']="ToDo";
    }

	function test_imageCheck(){
        return "ToDo1";    
    }

}