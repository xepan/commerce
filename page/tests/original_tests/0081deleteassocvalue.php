<?php

namespace xepan\commerce;

class page_tests_0081deleteassocvalue extends \xepan\base\Page_Tester{
	function init(){
		parent::init();
		$this->add('xepan\commerce\page_tests_init')
            ->createGenericCustomfields()
            ->createItems();
	}

	function prepare_associationdelete(){
        $this->proper_responses['test_associationdelete']= [
            'association_count'=>0,
            'customfield_count'=>0
        ];
    }

    function test_associationdelete(){
        $item = $this->add('xepan\commerce\Model_Item')->tryLoadAny();
        $generic = $this->add('xepan\commerce\Model_Item_CustomField_Generic')->tryloadAny();

        $association = $this->add('xepan\commerce\Model_Item_CustomField_Association');
        $association['item_id'] = $item->id;
        $association['customfield_generic_id'] = $generic->id;  
        $association->save();
                   
        $model_value = $this->add('xepan\commerce\Model_Item_CustomField_Value');
        $model_value['name'] = "val0";	
        $model_value['customfield_association_id'] = $association->id;	
        $model_value->save();
		
        $association->delete();        

        $row_count = [];
        $row_count['association_count'] = $this->app->db->dsql()
                          ->table('customfield_association')
                          ->field('count(*)')
                          ->getOne();
        
        $row_count['customfield_count'] = $this->app->db->dsql()
                          ->table('customfield_value')
                          ->field('count(*)')
                          ->getOne();

        return $row_count;
    }
}