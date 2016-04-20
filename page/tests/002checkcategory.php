<?php
namespace xepan\commerce;

class page_tests_002checkcategory extends \xepan\base\Page_Tester {
    public $title = 'Category Tests';

    public $proper_responses=[
    '-'=>'-'
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init')
            ->createCateories()
            ->createItems();
       
        parent::init();
    }

    function prepare_parentCategory(){
        $this->proper_responses['test_parentCategory']=[
            'parent_category_id'=>'',
            'name'=>'Test Category0',
            'display_sequence'=>'0',
            'alt_text'=>'Test0',
            'description'=>'Test0',
            'custom_link'=>'Test0',
            'meta_title'=>'Test0',
            'meta_description'=>'Test0',
            'meta_keywords'=>'Test0'
        ];
    }

    function test_parentCategory(){
        $this->category = $this->add('xepan\commerce\Model_Category')
                                    ->loadBy('name','Test Category0');                                                             
        $result=[];
        foreach ($this->proper_responses
            ['test_parentCategory'] as $field => $value) {
            $result[$field] = $this->category[$field];            
        }

        return $result;    
    }

    function prepare_childCategory(){
        $this->proper_responses['test_childCategory']=[
            'parent_category_id'=>$this->category->id,
            'name'=>'Test Category1',
            'display_sequence'=>'1',
            'alt_text'=>'Test1',
            'description'=>'Test1',
            'custom_link'=>'Test1',
            'meta_title'=>'Test1',
            'meta_description'=>'Test1',
            'meta_keywords'=>'Test1'
        ];
    }

    function test_childCategory(){
        $category = $this->add('xepan\commerce\Model_Category')
                                    ->loadBy('name','Test Category1');                                                             
        $result=[];
        foreach ($this->proper_responses
            ['test_childCategory'] as $field => $value) {
            $result[$field] = $category[$field];            
        }

        return $result;    
    }

    function prepare_categoryItemAssoc(){
        $this->proper_responses['test_categoryItemAssoc']=[
            'count'=>0
        ];
    }

    function test_categoryItemAssoc(){
        $count = $this->category->ref('xepan\commerce\CategoryItemAssociation')
                         ->count()->getOne();

        return ['count'=> $count];
    }

    function prepare_statusActive(){
        $this->proper_responses['test_statusActive']='Active';
    }

    function test_statusActive(){
        $this->category->activate();
         
        return $this->category['status']; 
    }

    function prepare_statusDeactive(){
        $this->proper_responses['test_statusDeactive']='DeActive';
    }

    function test_statusDeactive(){
        $this->category->deactivate();
 
        return $this->category['status'];
    }
}