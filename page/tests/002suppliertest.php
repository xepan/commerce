<?php
namespace xepan\commerce;

class page_tests_002suppliertest extends \xepan\base\Page_Tester {
    public $title = 'Supplier Tests';

    public $proper_responses=[
        '-'=>'-'
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init');
        parent::init();
    }

    function prepare_supplierCreation(){
        $this->proper_responses['test_supplierCreation']=[
            'epan_id'=>$this->app->epan->id,
            'created_by_id'=>$this->app->employee->id,
            'user_id'=>null,
            'type'=>'Supplier',
            'name'=>'Supplier1 Sirname',
            'status'=>'Active',
            'created_at'=>true,
        ];
    }

    function test_supplierCreation(){
        
        $supl = $this->add('xepan\commerce\Model_Supplier')->loadBy('first_name','Supplier1');

        $result=[];
        foreach ($this->proper_responses['test_supplierCreation'] as $field => $value) {
            $result[$field] = $supl[$field];
        }

        return $result;
    }
    
}