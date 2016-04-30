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
        $this->add('xepan\commerce\page_tests_init')->resetDB();
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
        $old_depts = $this->pdb->dsql()->table('xhr_departments')
                        ->where('name','<>','Company')
                        ->where('production_level','>=',1)
                        ->where('production_level','<=',100)
                        ->get();
        foreach ($old_depts as $dept){
                $new_dept
                ->set('name',$dept['name'])
                ->set('production_level',$dept['production_level'])
                ->set('is_outsourced',$dept['is_outsourced'])
                ->saveAndUnload()
                ;
        }
    }

    function test_ImportDepartments(){

    }

}
