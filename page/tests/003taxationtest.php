<?php
namespace xepan\commerce;

class page_tests_003taxationtest extends \xepan\base\Page_Tester {
    public $title = 'Taxation Tests';

    public $proper_responses=[
        '-'=>'-'
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init');
        parent::init();
    }

    function prepare_taxCreation(){
        $this->proper_responses['test_taxCreation']=[
            'user_id'=>null,
            'type'=>'Taxation',
            'name'=>'VatTax'
        ];
    }

    function test_taxCreation(){
        
        $tx = $this->add('xepan\commerce\Model_Taxation')->loadBy('name','VatTax');

        $result=[];
        foreach ($this->proper_responses['test_taxCreation'] as $field => $value) {
            $result[$field] = $tx[$field];
        }

        return $result;
    }
    
}