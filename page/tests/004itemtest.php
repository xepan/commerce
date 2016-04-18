<?php
namespace xepan\commerce;

class page_tests_004itemtest extends \xepan\base\Page_Tester {
    public $title = 'Item Tests';

    public $proper_responses=[
        '-'=>'-'
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init');
        parent::init();
    }

    function prepare_itemCreation(){
        $this->lead = $this->proper_responses['test_itemCreation']=[
            'epan_id'=>$this->app->epan->id,
            'created_by_id'=>$this->app->employee->id,
            'user_id'=>null,
            'type'=>'Item',
            'status'=>'UnPublished',
            'name'=>'Item1',
            'sku'=>'48848'
        ];
    }

    function test_itemCreation(){
        
        $itm = $this->add('xepan\commerce\Model_Item')->loadBy('name','Item1');

        $result=[];
        foreach ($this->proper_responses['test_itemCreation'] as $field => $value) {
            $result[$field] = $itm[$field];
        }

        return $result;
    }

    function prepare_itemCategory(){
        $this->proper_responses['test_itemCreation']=[
            'epan_id'=>$this->app->epan->id,
            'created_by_id'=>$this->app->employee->id,
            'user_id'=>null,
            'type'=>'CategoryItemAssociation',
            'source'=>$this->lead['source']
        ];
    }
    
    function test_Categories(){
        $categories = $this->add('xepan\commerce\Model_CategoryItemAssociation')->loadBy('name','Item1');
       
        $result=[];
        foreach ($this->proper_responses['test_itemCreation'] as $field => $value) {
            $result[$field] = $categories[$field];
        }

        return $result;
    }
}