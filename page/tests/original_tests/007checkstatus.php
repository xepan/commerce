<?php
namespace xepan\commerce;

class page_tests_007checkstatus extends \xepan\base\Page_Tester {
    public $title = 'Item Status Tests';

    public $proper_responses=[
        'test_ActionPublish'=>'Published',
        'test_ActionUnpublish'=>'UnPublished'
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init');

        $this->item = $this->add('xepan\commerce\Model_Item');
        $this->item->loadBy('name','Test0');
        
        parent::init();
    }

    function test_ActionPublish(){
        
        $this->item->publish();
        return $this->item['status'];
    }

    function test_ActionUnpublish(){
        $this->item->unpublish();
        return $this->item['status'];
    }
}