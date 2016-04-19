<?php
namespace xepan\commerce;



class page_tests_001customertest extends \xepan\base\Page_Tester {
    public $title = 'Customer Tests';

    public $proper_responses=[
        '-'=>'-'
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init');
        parent::init();
    }

    function prepare_customerCreation(){
        $this->proper_responses['test_customerCreation']=[
            'epan_id'=>$this->app->epan->id,
            'created_by_id'=>$this->app->employee->id,
            'user_id'=>null,
            'type'=>'Customer',
            'name'=>'Customer1 Sirname',
            'status'=>'Active',
            'created_at'=>true,
        ];
    }

    function test_customerCreation(){
        $cst = $this->add('xepan\commerce\Model_Customer')->loadBy('first_name','Customer1');

        $result=[];
        foreach ($this->proper_responses['test_customerCreation'] as $field => $value) {
            $result[$field] = $cst[$field];
        }

        return $result;
    }
    
}