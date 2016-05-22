<?php

namespace xepan\commerce;

class page_tests_0032specificationitemassoc extends \xepan\base\Page_Tester{
	public $title = "Item Specification Association Tests";
	public $proper_responses=[
    '-'=>'-'
    ];

	function init(){
		$this->add('xepan\commerce\page_tests_init')
            ->createItems();	

		parent::init();
	}

	// function prepare_itemSpecassocCount(){
 //        $this->proper_responses['test_itemSpecassocCount']= 1;
 //    }

 //    function test_itemSpecassocCount(){
    	                                                             
 //        $this->item = $this->add('xepan\commerce\Model_Item')
 //                                    ->loadBy('name','Test0'); 

 //        $this->assoc = $this->add('xepan\commerce\Model_CategoryItemAssociation');
 //        $this->assoc['item_id'] = $this->item->id; 
 //        $this->assoc['category_id'] = $this->category->id;                            
 //        $this->assoc->save();

 //        $result = $this->category->ref('xepan\commerce\CategoryItemAssociation')
 //                       ->addCondition('item_id',$this->item->id)
 //                       ->count()->getOne();

 //        return $result;    
 //    }

}