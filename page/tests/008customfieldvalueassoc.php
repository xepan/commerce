<?php

namespace xepan\commerce;

class page_tests_008customfieldvalueassoc extends \xepan\base\Page_Tester{
	public $title = "Customfield Generic, Value, Association Tests";
	public $proper_responses=[
    '-'=>'-'
    ];

	function init(){
		$this->add('xepan\commerce\page_tests_init')
            ->createGenericCustomfields()
            // ->createCustomfieldvalue()
            ->createItems();
            // ->customfieldassoc();

		parent::init();
	}

	function prepare_itemassocCount(){
        $this->proper_responses['test_itemassocCount']= [
            'itemassoc'=>1
        ];
    }

    function test_itemassocCount(){
    	
    	$this->assoc = $this->add('xepan\commerce\Model_Item_CustomField_Association')->tryLoadAny();
                                                                                                 
        $this->generic = $this->add('xepan\commerce\Model_Item_CustomField_Generic')
                              ->loadBy('name','cf0');

        $this->item = $this->add('xepan\commerce\Model_Item')
                                    ->loadBy('name','Test0');

        $this->assoc['item_id'] = $this->item->id;
        $this->assoc['customfield_generic_id'] = $this->generic->id;
        $this->assoc->save();


       $count = $this->item->ref('xepan\commerce\Item_CustomField_Association')->count()->getOne();


       return ['itemassoc'=>$count];                            
    }


    function prepare_genericassocCount(){
        $this->proper_responses['test_genericassocCount']= [
            'genericassoc'=>1
        ];
    }

    function test_genericassocCount(){        
       $count = $this->generic->ref('xepan\commerce\Item_CustomField_Association')->count()->getOne();

       return ['genericassoc'=>$count];                            
    }


    function prepare_valueassocCount(){
        $this->proper_responses['test_valueassocCount']= [
            'valueassoc'=>1
        ];
    }

    function test_valueassocCount(){        
       
       $this->value = $this->add('xepan\commerce\Model_Item_CustomField_Value');
       $this->value['name'] = 'val0';
       $this->value['customfield_association_id'] = $this->assoc->id; 
       $this->value->save();        

       $count = $this->assoc->ref('xepan\commerce\Item_CustomField_Value')->count()->getOne();

       return ['valueassoc'=>$count];                            
    }


    function prepare_multiitemassocCount(){
        $this->proper_responses['test_multiitemassocCount']= [
            'itemassoc'=>4
        ];
    }

    function test_multiitemassocCount(){        
        // $item_id = $this->item->id;
        $generic_id = $this->generic->id;

        for($i=0;$i<3;$i++){
            $association = $this->add('xepan\commerce\Model_Item_CustomField_Association');
            $association['item_id'] = $this->item->id;
            $association['customfield_generic_id'] = ++$generic_id;
            $association->save();
        }
        
        $count = $this->item->ref('xepan\commerce\Item_CustomField_Association')->count()->getOne();
        return ['itemassoc'=>$count];
    }

    function prepare_itemdelete(){
        $this->proper_responses['test_itemdelete']= [
            'item_count'=>3,
            'association_count'=>0
        ];
    }

    function test_itemdelete(){
        
        $row_count = [];
        $model_item = $this->add('xepan\commerce\Model_item');
        $model_item->loadBy('id',$this->item->id);
        $model_item->delete();        
        
        $row_count['item_count'] = $this->app->db->dsql()
                          ->table('item')
                          ->field('count(*)')
                          ->getOne();
        
        $row_count['association_count'] = $this->app->db->dsql()
                          ->table('customfield_association')
                          ->field('count(*)')
                          ->getOne();

        return $row_count;
    }
}