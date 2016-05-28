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
        
    ];


    function init(){
        // $this->add('xepan\commerce\page_tests_init')->resetDB();

        $this->pdb = $this->add('DB')->connect($this->app->getConfig('dsn2'));
        
        try{
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=0;')->execute();
            $this->app->db->dsql()->expr('SET autocommit=0;')->execute();

            $this->api->db->beginTransaction();
                parent::init();
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
            $this->api->db->commit();
        }catch(\Exception_StopInit $e){

        }catch(\Exception $e){
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
            $this->api->db->rollback();
            throw $e;
        }
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['count'] = $this->add('xepan\commerce\Model_Item_CustomField_Value')
                            ->addCondition('customfield_type','CustomField')
                            ->count()->getOne();
    	return $result;
    }

    function prepare_Import_Association() {
        $this->proper_responses['test_Import_Association']['count'] = $this->pdb->dsql()->table('xshop_item_customfields_assos')->del('fields')->field('count(*)')->getOne();

        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');

        $customfield_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('customfield');

        $department_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('department');

        
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
        $count = $this->add('xepan\commerce\Model_Item_CustomField_Association')
                            ->addCondition('CustomFieldType','CustomField')
                            ->count()->getOne();
        return ['count'=>$count];
    }

}
