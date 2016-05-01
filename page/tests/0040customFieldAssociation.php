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

class page_tests_0040customFieldAssociation extends \xepan\base\Page_Tester {
	
	public $title='Item Custom Field Association';
	
	public $proper_responses=[
       
    	'test_checkEmptyRows'=>['count'=>0],
        'test_Import_Association'=>['count'=>-1]
        
    ];


    function init(){
        $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['count'] = $this->app->db->dsql()->table('customfield_association')->del('fields')->field('count(*)')->getOne();
    	return $result;
    }

    function prepare_Import_Association() {
        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');

        $customfield_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('customfield');

        $department_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('department');

        $this->proper_responses['test_Import_Association']['count'] = $this->pdb->dsql()->table('xshop_item_customfields_assos')->del('fields')->field('count(*)')->getOne();
        
        $new_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association');

        $old_assos = $this->pdb->dsql()->table('xshop_item_customfields_assos')
                            ->get()
                            ;
        $file_data = [];
        foreach ($old_assos as $old_asso) {
            $new_customfield_id = isset($customfield_mapping[$old_asso['customfield_id']])?$customfield_mapping[$old_asso['customfield_id']]['new_id']:0;
            $new_item_id = isset($item_mapping[$old_asso['item_id']])?$item_mapping[$old_asso['item_id']]['new_id']:0;
            $new_department_id = isset($department_mapping[$old_asso['department_id']])?$department_mapping[$old_asso['department_id']]['new_id']:0;

            $new_asso
            ->set('customfield_generic_id',$new_customfield_id)
            ->set('item_id',$new_item_id)
            ->set('department_id',$new_department_id)
            ->set('can_effect_stock',$old_asso['can_effect_stock'])
            ->set('status',$old_asso['is_active']?"Active":"DeActivate")
            ->save();

            $file_data[$old_asso['id']] = ['new_id'=>$new_asso->id];
            $new_asso->unload();
        }
        
        file_put_contents(__DIR__.'/customfield_association_mapping.json', json_encode($file_data));
    }

    function test_Import_Association(){
        $count = $this->app->db->dsql()->table('customfield_association')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$count];
    }

}
