<?php

namespace xepan\commerce;

class page_tests_0031categoryitemassoc extends \xepan\base\Page_Tester{
	public $title = "Item Category Association Tests";
	public $proper_responses=[
    '-'=>'-'
    ];

	function init(){
		$this->add('xepan\commerce\page_tests_init')
            ->createCateories()
            ->createItems();	

		parent::init();
	}

	function prepare_singleAssociationCount(){
        $this->proper_responses['test_singleAssociationCount']= 1;
    }

    function test_singleAssociationCount(){
    	
    	$this->category = $this->add('xepan\commerce\Model_Category')
                                    ->loadBy('name','Test Category0');                                                             
        $this->item = $this->add('xepan\commerce\Model_Item')
                                    ->loadBy('name','Test0'); 

        $this->assoc = $this->add('xepan\commerce\Model_CategoryItemAssociation');
        $this->assoc['item_id'] = $this->item->id; 
        $this->assoc['category_id'] = $this->category->id;                            
        $this->assoc->save();

        $result = $this->category->ref('xepan\commerce\CategoryItemAssociation')
                       ->addCondition('item_id',$this->item->id)
                       ->count()->getOne();

        return $result;    
    }

    function prepare_multipleAssociationCount(){
        $this->proper_responses['test_multipleAssociationCount']= 4;
    }

    function test_multipleAssociationCount(){
    	
    	$this->category = $this->add('xepan\commerce\Model_Category')
                                    ->loadBy('name','Test Category1');                                                             
        $this->item =$model_item =  $this->add('xepan\commerce\Model_Item');

        foreach ($model_item as $item) {
		    $this->assoc = $this->add('xepan\commerce\Model_CategoryItemAssociation');
		    $this->assoc['item_id'] = $item->id; 
		    $this->assoc['category_id'] = $this->category->id;           
		    $this->assoc->save();
        }

        $result = $this->category->ref('xepan\commerce\CategoryItemAssociation')
                       ->addCondition('category_id',$this->category->id)
                       ->count()->getOne();

        return $result;    
    }

}