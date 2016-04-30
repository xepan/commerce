<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;


class page_tests_0100salesOrder extends \xepan\base\Page_Tester {
	
	public $title='Sales Order Importer';

	public $proper_responses=[
		'test_importSalesOrder'=>'43'
	];

	function init(){
        set_time_limit(0);
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function prepare_importSalesOrder(){

    }

    function test_importSalesOrder(){
    	return "TODO";
    }

}
