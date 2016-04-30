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

class page_tests_0010departmentImport extends \xepan\base\Page_Tester {
	
	public $title='Department Importer';
	
	public $proper_responses=[
    	'test_checkEmptyRows'=>['department'=>1],
    	'test_ImportDepartments'=>['Company','']
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init');
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['department'] = $this->app->db->dsql()->table('department')->del('fields')->field('count(*)')->getOne();

    	return $result;
    }

    function prepare_ImportDepartments(){
        $new_dept = $this->add('xepan\hr\Model_Department');
        foreach ($this->pdb->dsql()->table('xhr_departments')->get() as $dept){
                $new_dept
                ->set('name',$dept['Name'])
                ->set('production_level',$dept['production_level'])
                ->saveAndUnload()
                ;
        }
    }

    function test_ImportDepartments(){

    }

}
