<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class page_tests_0050customFieldAssociationValue extends \xepan\base\Page_Tester {
	
	public $title='Item Custom Field Association Value';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_Value'=>['count'=>-1]
        
    ];


    function init(){
        
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
        $result=[];
        $count = $this->add('xepan\commerce\Model_Item_CustomField_Value')
                            ->addCondition('customfield_type','CustomField')
                            ->count()->getOne();
        $result['count'] = $count;
        return $result;
    }

    function prepare_Import_Value() {
        $this->proper_responses['test_Import_Value']['count'] = $this->pdb->dsql()->table('xshop_custom_fields_value')->del('fields')->field('count(*)')->getOne();
        
        $association_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('customfield_association');

        
        $new_value = $this->add('xepan\commerce\Model_Item_CustomField_Value');

        $old_values = $this->pdb->dsql()->table('xshop_custom_fields_value')
                            ->get()
                            ;
        $file_data = [];
        foreach ($old_values as $old_value) {
            $new_asso_id = isset($association_mapping[$old_value['itemcustomfiledasso_id']])?$association_mapping[$old_value['itemcustomfiledasso_id']]['new_id']:0;

            $new_value
            ->set('customfield_association_id',$new_asso_id)
            ->set('name',$old_value['name'])
            ->set('status',$old_value['is_active']?"Active":"DeActive")
            ->save();

            $file_data[$old_value['id']] = ['new_id'=>$new_value->id,'name'=>$new_value['name']];
            $new_value->unload();
        }
        
        file_put_contents(__DIR__.'/customfield_association_value_mapping.json', json_encode($file_data));
    }

    function test_Import_Value(){
        $count = $this->add('xepan\commerce\Model_Item_CustomField_Value')
                            ->addCondition('customfield_type','CustomField')
                            ->count()->getOne();
        return ['count'=>$count];
    }

}
