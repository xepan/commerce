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

class page_tests_0035SpecificationAssociationValue extends \xepan\base\Page_Tester {
	
	public $title='Specification Association and Value Importer';
	
	public $proper_responses=[

        'test_checkEmpty_Specification_Association'=>['count'=>0],
        'test_Import_Specification_Association'=>['count'=>-1],
        
        'test_checkEmpty_Specification_Value'=>['count'=>0],
        'test_Import_Specification_Value'=>['count'=>-1]

    ];


    function init(){
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        
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


    function test_checkEmpty_Specification_Association(){
        return [
                    'count'=>  $this->add('xepan\commerce\Model_Item_CustomField_Association')->addCondition('CustomFieldType','Specification')->count()->getOne()
                ];
    }

    function test_checkEmpty_Specification_Value(){
        return [ 
                "count" => $this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_type','Specification')->count()->getOne()
            ];
    }

    function prepare_Import_Specification_Association(){
        $this->proper_responses['test_Import_Specification_Association']['count'] = $this->pdb->dsql()->table('xshop_item_spec_ass')->del('fields')->field('count(*)')->getOne();

        $item_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('item');

        $specification_mapping = $this->add('xepan\commerce\page_tests_init')
                            ->getMapping('specification');

        $file_data = [];
        $old_associations = $this->pdb->dsql()->table('xshop_item_spec_ass')->get();

        $new_spec_asso = $this->add('xepan\commerce\Model_Item_CustomField_Association');

        foreach ($old_associations as $old_asso) {
            $new_spec_asso
                ->set('customfield_generic_id',$specification_mapping[$old_asso['specification_id']]['new_id'])
                ->set('item_id',$item_mapping[$old_asso['item_id']]['new_id'])
                ->set('status',"Active")
                ->save();

            //Value Entry
            $value_model = $this->add('xepan\commerce\Model_Item_CustomField_Value');
            $value_model
                ->set('customfield_association_id',$new_spec_asso->id)
                ->set('name',$old_asso['value'])
                ->set('highlight_it',$old_asso['highlight_it'])
                ->saveAndUnload();
                ;

            $file_data[$old_asso['id']] = ['new_id'=>$new_spec_asso->id];
            $new_spec_asso->unload();     
        }
        
        file_put_contents(__DIR__.'/specification_association_mapping.json', json_encode($file_data));
    }

    function test_Import_Specification_Association(){
        $count = $this->add('xepan\commerce\Model_Item_CustomField_Association')
                            ->addCondition('CustomFieldType','Specification')
                            ->count()->getOne();
        return ['count'=>$count];
    }

    function test_Import_Specification_Value(){
        $this->proper_responses['test_Import_Specification_Value']['count'] = $this->pdb->dsql()->table('xshop_item_spec_ass')->del('fields')->field('count(*)')->getOne();

        $count = $this->add('xepan\commerce\Model_Item_CustomField_Value')
                            ->addCondition('customfield_type','Specification')
                            ->count()->getOne();
        return ['count'=>$count];
    }


}
