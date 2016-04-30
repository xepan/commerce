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
    	'test_ImportDepartments'=>['Company','Designing','Offset Printing','Digital Press','Large Format','Screen Printing','Varnish','Lamination','UV','Foil','Cutting','Die Cut','Laser Cut','Binding','Pasting','Frame']
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
        $file_data=[];
        foreach ($old_depts as $dept){
                $new_dept
                ->set('name',$dept['name'])
                ->set('production_level',$dept['production_level'])
                ->set('is_outsourced',$dept['is_outsourced'])
                ->save()
                ;

                $file_data[$dept['id']] = ['new_id'=>$new_dept->id];

                $new_dept->unload();
        }

        file_put_contents(__DIR__.'/department_mapping.json', json_encode($file_data));
    }

    function test_ImportDepartments(){
        $new_depts = $this->api->db->dsql()->table('department')->get();

        $result = [];
        foreach ($new_depts as $d) {
            $result[] = $d['name'];
        }

        return $result;
    }

}
