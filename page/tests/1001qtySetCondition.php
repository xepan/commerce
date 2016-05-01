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

class page_tests_1001qtySetCondition extends \xepan\base\Page_Tester {
	
	public $title='Qty Set Condition Importer';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_QtySetConditions'=>['count'=>-1]
        
    ];


    function init(){
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['count'] = $this->app->db->dsql()->table('quantity_condition')->del('fields')->field('count(*)')->getOne();
    	return $result;
    }

    function prepare_Import_QtySetConditions(){

        $qtyset_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('qtyset');
        $cf_value_mapping = $this->add('xepan\commerce\page_tests_init')
                    ->getMapping('customfield_association_value');

        $this->proper_responses['test_Import_QtySetConditions']['count'] = $this->pdb->dsql()->table('xshop_item_quantity_set_conditions')->del('fields')->field('count(*)')->getOne();

        $new_condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition');

        $old_conditions = $this->pdb->dsql()->table('xshop_item_quantity_set_conditions')
                            ->get();

        $file_data = [];
        foreach ($old_conditions as $old_condition) {
            $new_condition
            ->set('quantity_set_id',$qtyset_mapping[$old_condition['quantityset_id']]['new_id'])
            ->set('customfield_value_id',$cf_value_mapping[$old_condition['custom_field_value_id']]['new_id'])
            ->save();

            $file_data[$old_condition['id']] = ['new_id'=>$new_condition->id];
            $new_condition->unload();
        }
        
        file_put_contents(__DIR__.'/qtyset_condition_mapping.json', json_encode($file_data));
    }

    function test_Import_QtySetConditions(){
        $set_count = $this->app->db->dsql()->table('quantity_condition')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$set_count];
    }

}
